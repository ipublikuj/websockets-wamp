<?php
/**
 * OnServerStartHandler.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     Events
 * @since          1.0.0
 *
 * @date           01.03.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Events;

use Nette;

use React\EventLoop\LoopInterface;

use IPub;
use IPub\WebSocketsWAMP\Application;
use IPub\WebSocketsWAMP\PushMessages;

/**
 * Server start event for push managers
 *
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     Events
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
	public function __invoke(LoopInterface $eventLoop)
	{
		/** @var PushMessages\IConsumer $consumer */
		foreach ($this->consumersRegistry->getConsumers() as $consumer) {
			$consumer->connect($eventLoop, $this->application);
		}
	}
}
