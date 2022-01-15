<?php declare(strict_types = 1);

namespace Tests\Cases;

use IPub\WebSocketsWAMP\Application;
use IPub\WebSocketsWAMP\Clients;
use IPub\WebSocketsWAMP\PushMessages;
use IPub\WebSocketsWAMP\Serializers;
use IPub\WebSocketsWAMP\Subscribers;
use IPub\WebSocketsWAMP\Topics;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../BaseTestCase.php';

/**
 * @testCase
 */
final class ExtensionTests extends BaseTestCase
{

	public function testFunctional(): void
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('webSocketsWAMP.topics.driver.memory') instanceof Topics\Drivers\InMemory);
		Assert::true($dic->getService('webSocketsWAMP.topics.storage') instanceof Topics\IStorage);

		Assert::true($dic->getService('webSocketsWAMP.application') instanceof Application\IApplication);

		Assert::true($dic->getService('webSocketsWAMP.serializer') instanceof Serializers\PushMessageSerializer);
		Assert::true($dic->getService('webSocketsWAMP.push.registry') instanceof PushMessages\ConsumersRegistry);

		Assert::true($dic->getService('webSocketsWAMP.clients.factory') instanceof Clients\ClientFactory);

		Assert::true($dic->getService('webSocketsWAMP.subscribers.onServerStart') instanceof Subscribers\OnServerStartHandler);
	}

}

$test_case = new ExtensionTests();
$test_case->run();
