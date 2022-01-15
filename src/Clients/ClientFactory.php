<?php declare(strict_types = 1);

/**
 * ClientFactory.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Clients
 * @since          1.0.0
 *
 * @date           06.03.17
 */

namespace IPub\WebSocketsWAMP\Clients;

use IPub\WebSockets\Clients as WebSocketsClients;
use IPub\WebSockets\Entities as WebSocketsEntities;
use IPub\WebSocketsWAMP\Entities;
use React\Socket;

/**
 * WAMP client connection factory
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Clients
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class ClientFactory implements WebSocketsClients\IClientFactory
{

	/**
	 * {@inheritdoc}
	 */
	public function create(int $id, Socket\ConnectionInterface $connection): WebSocketsEntities\Clients\IClient
	{
		return new Entities\Clients\Client($id, $connection);
	}

}
