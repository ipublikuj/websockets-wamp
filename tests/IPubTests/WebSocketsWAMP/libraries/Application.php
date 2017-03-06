<?php
/**
 * Test: IPub\WebSocketsWAMP\Libraries
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketsSession!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           06.03.17
 */

declare(strict_types = 1);

namespace IPubTests\WebSocketsWAMP\Libraries;

use IPub\WebSocketsWAMP;
use IPub\WebSocketsWAMP\Application\V1\IApplication;
use IPub\WebSocketsWAMP\Entities\PushMessages;
use IPub\WebSockets\Entities;
use IPub\WebSockets\Http;

class Application implements IApplication
{
	/**
	 * {@inheritdoc}
	 */
	function onOpen(Entities\Clients\IClient $client, Http\IRequest $httpRequest)
	{

	}

	/**
	 * {@inheritdoc}
	 */
	function onClose(Entities\Clients\IClient $client, Http\IRequest $httpRequest)
	{

	}

	/**
	 * {@inheritdoc}
	 */
	function onError(Entities\Clients\IClient $client, Http\IRequest $httpRequest, \Exception $ex)
	{

	}

	/**
	 * {@inheritdoc}
	 */
	function onMessage(Entities\Clients\IClient $from, Http\IRequest $httpRequest, string $message)
	{

	}

	public function onPush(PushMessages\IMessage $message, string $provider)
	{

	}

	/**
	 * {@inheritdoc}
	 */
	function getSubProtocols() : array
	{
		return [];
	}
}
