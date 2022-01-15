<?php declare(strict_types = 1);

/**
 * IMessage.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           28.02.17
 */

namespace IPub\WebSocketsWAMP\Entities\PushMessages;

/**
 * A push message interface
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IMessage
{

	/**
	 * @return string
	 */
	public function getTopic(): string;

	/**
	 * @return array
	 */
	public function getData(): array;

}
