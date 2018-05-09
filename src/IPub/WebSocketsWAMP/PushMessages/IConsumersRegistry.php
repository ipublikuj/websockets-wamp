<?php
/**
 * IConsumersRegistry.php
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

/**
 * Server push consumers registry interface
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     PushMessages
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IConsumersRegistry
{
	/**
	 * @param IConsumer $consumer
	 *
	 * @return void
	 */
	function addConsumer(IConsumer $consumer) : void;

	/**
	 * @param string $name
	 *
	 * @return IConsumer
	 */
	function getConsumer(string $name) : IConsumer;

	/**
	 * @return IConsumer[]
	 */
	function getConsumers(): array;
}
