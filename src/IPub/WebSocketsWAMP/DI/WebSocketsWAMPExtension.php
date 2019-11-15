<?php
/**
 * WebSocketsWAMPExtension.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           01.03.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\DI;

use Nette;
use Nette\DI;

use Symfony\Component\EventDispatcher;

use IPub\WebSocketsWAMP;
use IPub\WebSocketsWAMP\Application;
use IPub\WebSocketsWAMP\Clients;
use IPub\WebSocketsWAMP\Events;
use IPub\WebSocketsWAMP\PushMessages;
use IPub\WebSocketsWAMP\Serializers;
use IPub\WebSocketsWAMP\Subscribers;
use IPub\WebSocketsWAMP\Topics;

use IPub\WebSockets\Server as WebSocketsServer;
use IPub\WebSockets\Clients as WebSocketsClients;

/**
 * WebSockets WAMP extension container
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class WebSocketsWAMPExtension extends DI\CompilerExtension
{
	/**
	 * @var array
	 */
	private $defaults = [
		'storage'       => [
			'topics' => [
				'driver' => '@topics.driver.memory',
				'ttl'    => 0,
			],
		],
		'symfonyEvents' => FALSE,
	];

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration() : void
	{
		parent::loadConfiguration();

		/** @var DI\ContainerBuilder $builder */
		$builder = $this->getContainerBuilder();
		/** @var array $configuration */
		if (method_exists($this, 'validateConfig')) {
			$configuration = $this->validateConfig($this->defaults);
		} else {
			$configuration = $this->getConfig($this->defaults);
		}

		if ($configuration['storage']['topics']['driver'] === '@topics.driver.memory') {
			$storageDriver = $builder->addDefinition($this->prefix('topics.driver.memory'))
				->setType(Topics\Drivers\InMemory::class);

		} else {
			$storageDriver = $builder->getDefinition($this->prefix('topics.driver.memory'));
		}

		$builder->addDefinition($this->prefix('topics.storage'))
			->setType(Topics\Storage::class)
			->setArguments([
				'ttl' => $configuration['storage']['topics']['ttl'],
			])
			->addSetup('?->setStorageDriver(?)', ['@' . $this->prefix('topics.storage'), $storageDriver]);

		$builder->addDefinition($this->prefix('application'))
			->setType(Application\Application::class);

		$builder->addDefinition($this->prefix('serializer'))
			->setType(Serializers\PushMessageSerializer::class);

		/**
		 * PUSH NOTIFICATION
		 */

		$builder->addDefinition($this->prefix('push.registry'))
			->setType(PushMessages\ConsumersRegistry::class);

		/**
		 * CLIENTS
		 */

		$builder->removeDefinition($builder->getByType(WebSocketsClients\IClientFactory::class));

		$builder->addDefinition($this->prefix('clients.factory'))
			->setType(Clients\ClientFactory::class);

		/**
		 * SUBSCRIBERS
		 */

		$builder->addDefinition($this->prefix('subscribers.onServerStart'))
			->setType(Subscribers\OnServerStartHandler::class);

		$server = $builder->getDefinitionByType(WebSocketsServer\Server::class);
		$server->addSetup('$service->onStart[] = ?', ['@' . $this->prefix('subscribers.onServerStart')]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile() : void
	{
		parent::beforeCompile();

		/** @var DI\ContainerBuilder $builder */
		$builder = $this->getContainerBuilder();
		/** @var array $configuration */
		if (method_exists($this, 'validateConfig')) {
			$configuration = $this->validateConfig($this->defaults);
		} else {
			$configuration = $this->getConfig($this->defaults);
		}

		$registry = $builder->getDefinition($builder->getByType(PushMessages\ConsumersRegistry::class));

		$consumers = $builder->findByType(PushMessages\IConsumer::class);

		foreach ($consumers as $consumer) {
			$registry->addSetup('?->addConsumer(?)', [$registry, $consumer]);
		}

		/**
		 * EVENTS
		 */

		if ($configuration['symfonyEvents'] === TRUE) {
			$dispatcher = $builder->getDefinition($builder->getByType(EventDispatcher\EventDispatcherInterface::class));

			$application = $builder->getDefinition($builder->getByType(Application\Application::class));
			assert($application instanceof DI\ServiceDefinition);

			$application->addSetup('?->onPush[] = function() {?->dispatch(new ?(...func_get_args()));}', [
				'@self',
				$dispatcher,
				new Nette\PhpGenerator\PhpLiteral(Events\Application\PushEvent::class),
			]);
		}
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(Nette\Configurator $config, string $extensionName = 'webSocketsWAMP') : void
	{
		$config->onCompile[] = function (Nette\Configurator $config, DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new WebSocketsWAMPExtension());
		};
	}
}
