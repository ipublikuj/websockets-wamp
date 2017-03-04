<?php
/**
 * IConsumersRegistry.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketWAMP!
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
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     PushMessages
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IConsumersRegistry
{
	/**
	 * @param IConsumer $consumer
	 * @param string $name
	 *
	 * @return void
	 */
	function addConsumer(IConsumer $consumer, string $name);

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
