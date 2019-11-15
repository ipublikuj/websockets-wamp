<?php
/**
 * IPusher.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     PushMessages
 * @since          1.0.0
 *
 * @date           28.02.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\PushMessages;

use IPub\WebSocketsWAMP\Entities;

/**
 * Server message pusher interface
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     PushMessages
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IPusher
{
	/**
	 * @param array|string $data
	 * @param string $destination
	 * @param array[] $routeParameters
	 * @param array $context
	 *
	 * @return void
	 */
	public function push($data, $destination, array $routeParameters = [], array $context = []) : void;

	/**
	 * @param bool $bool
	 *
	 * @return void
	 */
	public function setConnected(bool $bool = TRUE) : void;

	/**
	 * @return bool
	 */
	public function isConnected() : bool;

	/**
	 * @return string
	 */
	public function getName() : string;
}
