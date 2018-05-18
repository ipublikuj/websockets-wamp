<?php
/**
 * ClientFactory.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Clients
 * @since          1.0.0
 *
 * @date           06.03.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Clients;

use React\Socket;

use IPub\WebSocketsWAMP\Entities;

use IPub\WebSockets\Clients as WebSocketsClients;
use IPub\WebSockets\Entities as WebSocketsEntities;

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
	public function create(int $id, Socket\ConnectionInterface $connection) : WebSocketsEntities\Clients\IClient
	{
		return new Entities\Clients\Client($id, $connection);
	}
}
