<?php
/**
 * IConsumer.php
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

use React;
use React\EventLoop;

use IPub\WebSocketsWAMP\Application;

/**
 * Server push consumer interface
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     PushMessages
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IConsumer
{
	/**
	 * @param EventLoop\LoopInterface $loop
	 * @param Application\IApplication $application
	 *
	 * @return void
	 */
	public function connect(EventLoop\LoopInterface $loop, Application\IApplication $application);

	/**
	 * @return string
	 */
	public function getName() : string;

	/**
	 * @return void
	 */
	public function close() : void;
}
