<?php
/**
 * IMessage.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           28.02.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Entities\PushMessages;

/**
 * A push message interface
 *
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IMessage
{
	/**
	 * @return string
	 */
	function getTopic() : string;

	/**
	 * @return array
	 */
	function getData() : array;
}
