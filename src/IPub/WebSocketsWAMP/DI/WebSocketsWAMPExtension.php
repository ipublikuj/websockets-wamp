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

use IPub\WebSocketsWAMP;
use IPub\WebSocketsWAMP\Application;
use IPub\WebSocketsWAMP\Clients;
use IPub\WebSocketsWAMP\Events;
use IPub\WebSocketsWAMP\PushMessages;
use IPub\WebSocketsWAMP\Serializers;
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
 *
 * @method DI\ContainerBuilder getContainerBuilder()
 * @method array getConfig(array $default)
 * @method string prefix($id)
 */
final class WebSocketsWAMPExtension extends DI\CompilerExtension
{
	/**
	 * @var array
	 */
	private $defaults = [
		'storage' => [
			'topics'  => [
				'driver' => '@topics.driver.memory',
				'ttl'    => 0,
			],
		],
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
		$configuration = $this->getConfig($this->defaults);

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
		 * EVENTS
		 */

		$builder->addDefinition($this->prefix('events.onServerStart'))
			->setType(Events\OnServerStartHandler::class);

		$server = $builder->getDefinitionByType(WebSocketsServer\Server::class);
		$server->addSetup('$service->onStart[] = ?', ['@' . $this->prefix('events.onServerStart')]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile() : void
	{
		parent::beforeCompile();

		/** @var DI\ContainerBuilder $builder */
		$builder = $this->getContainerBuilder();

		$registry = $builder->getDefinition($builder->getByType(PushMessages\ConsumersRegistry::class));

		$consumers = $builder->findByType(PushMessages\IConsumer::class);

		foreach ($consumers as $consumer) {
			$registry->addSetup('?->addConsumer(?)', [$registry, $consumer]);
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
