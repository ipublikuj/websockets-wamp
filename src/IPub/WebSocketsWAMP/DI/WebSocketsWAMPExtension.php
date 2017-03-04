<?php
/**
 * WebSocketsWAMPExtension.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           01.03.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\DI;

use Nette;
use Nette\DI;

use IPub;
use IPub\WebSocketsWAMP;
use IPub\WebSocketsWAMP\Application;
use IPub\WebSocketsWAMP\Topics;

/**
 * WebSockets WAMP extension container
 *
 * @package        iPublikuj:WebSocketWAMP!
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
		'version' => 'v1',    // v1|v2
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
	public function loadConfiguration()
	{
		parent::loadConfiguration();

		/** @var DI\ContainerBuilder $builder */
		$builder = $this->getContainerBuilder();
		/** @var array $configuration */
		$configuration = $this->getConfig($this->defaults);

		if ($configuration['version'] === 'v1') {
			if ($configuration['storage']['topics']['driver'] === '@topics.driver.memory') {
				$storageDriver = $builder->addDefinition($this->prefix('topics.driver.memory'))
					->setClass(Topics\Drivers\InMemory::class);

			} else {
				$storageDriver = $builder->getDefinition($this->prefix('topics.driver.memory'));
			}

			$builder->addDefinition($this->prefix('topics.storage'))
				->setClass(Topics\Storage::class)
				->setArguments([
					'ttl' => $configuration['storage']['topics']['ttl'],
				])
				->addSetup('?->setStorageDriver(?)', ['@' . $this->prefix('topics.storage'), $storageDriver]);

			$builder->addDefinition($this->prefix('application'))
				->setClass(Application\V1\Application::class);
		}
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(Nette\Configurator $config, string $extensionName = 'websocketsWAMP')
	{
		$config->onCompile[] = function (Nette\Configurator $config, DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new WebSocketsWAMPExtension());
		};
	}
}
