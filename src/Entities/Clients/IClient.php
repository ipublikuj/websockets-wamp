<?php declare(strict_types = 1);

/**
 * IClient.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           06.03.17
 */

namespace IPub\WebSocketsWAMP\Entities\Clients;

use IPub\WebSockets\Entities as WebSocketsEntities;
use IPub\WebSocketsWAMP\Entities;

/**
 * WAMP single client connection interface
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IClient extends WebSocketsEntities\Clients\IClient
{

	/**
	 * @param Entities\Topics\ITopic $topic
	 * @param mixed $message
	 *
	 * @return void
	 */
	public function event(Entities\Topics\ITopic $topic, $message): void;

}
