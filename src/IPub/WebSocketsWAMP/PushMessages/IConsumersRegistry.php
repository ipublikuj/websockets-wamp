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
	public function addConsumer(IConsumer $consumer) : void;

	/**
	 * @param string $name
	 *
	 * @return IConsumer
	 */
	public function getConsumer(string $name) : IConsumer;

	/**
	 * @return IConsumer[]
	 */
	public function getConsumers() : array;
}
