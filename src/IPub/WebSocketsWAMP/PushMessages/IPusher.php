<?php
/**
 * IPusher.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     PushMessages
 * @since          1.0.0
 *
 * @date           28.02.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\PushMessages;

use IPub;
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
	 * @return string
	 */
	function push($data, $destination, array $routeParameters = [], array $context = []);

	/**
	 * @param bool $bool
	 *
	 * @return void
	 */
	function setConnected(bool $bool = TRUE);

	/**
	 * @return bool
	 */
	function isConnected() : bool;

	/**
	 * @return string
	 */
	function getName() : string;
}
