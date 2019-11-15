<?php
/**
 * OnServerStartHandler.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Subscribers
 * @since          1.0.0
 *
 * @date           01.03.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Subscribers;

use Nette;

use React\EventLoop\LoopInterface;

use IPub\WebSocketsWAMP\Application;
use IPub\WebSocketsWAMP\PushMessages;

/**
 * Server start event for push managers
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Subscribers
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class OnServerStartHandler
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var PushMessages\ConsumersRegistry
	 */
	private $consumersRegistry;

	/**
	 * @var Application\IApplication
	 */
	private $application;

	/**
	 * @param PushMessages\ConsumersRegistry $consumersRegistry
	 * @param Application\IApplication $application
	 */
	public function __construct(PushMessages\ConsumersRegistry $consumersRegistry, Application\IApplication $application)
	{
		$this->consumersRegistry = $consumersRegistry;
		$this->application = $application;
	}

	/**
	 * @param LoopInterface $eventLoop
	 */
	public function __invoke(LoopInterface $eventLoop) : void
	{
		/** @var PushMessages\IConsumer $consumer */
		foreach ($this->consumersRegistry->getConsumers() as $consumer) {
			$consumer->connect($eventLoop, $this->application);
		}
	}
}
