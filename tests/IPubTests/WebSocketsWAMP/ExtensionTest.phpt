<?php
/**
 * Test: IPub\WebSocketsWAMP\Extension
 *
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           06.03.17
 */

declare(strict_types = 1);

namespace IPubTests\WebSocketsWAMP;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\WebSocketsWAMP;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 * WebSockets WAMP extension container test case
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Tests
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class ExtensionTest extends Tester\TestCase
{
	public function testCompilersServices()
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('webSocketsWAMP.topics.driver.memory') instanceof WebSocketsWAMP\Topics\Drivers\InMemory);
		Assert::true($dic->getService('webSocketsWAMP.topics.storage') instanceof WebSocketsWAMP\Topics\IStorage);

		Assert::true($dic->getService('webSocketsWAMP.application') instanceof WebSocketsWAMP\Application\IApplication);

		Assert::true($dic->getService('webSocketsWAMP.serializer') instanceof WebSocketsWAMP\Serializers\PushMessageSerializer);
		Assert::true($dic->getService('webSocketsWAMP.push.registry') instanceof WebSocketsWAMP\PushMessages\ConsumersRegistry);

		Assert::true($dic->getService('webSocketsWAMP.clients.factory') instanceof WebSocketsWAMP\Clients\ClientFactory);

		Assert::true($dic->getService('webSocketsWAMP.subscribers.onServerStart') instanceof IPub\WebSocketsWAMP\Subscribers\OnServerStartHandler);
	}

	/**
	 * @return Nette\DI\Container
	 */
	protected function createContainer() : Nette\DI\Container
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		$config->addConfig(__DIR__ . DS . 'files' . DS . 'config.neon');

		WebSocketsWAMP\DI\WebSocketsWAMPExtension::register($config);

		return $config->createContainer();
	}
}

\run(new ExtensionTest());
