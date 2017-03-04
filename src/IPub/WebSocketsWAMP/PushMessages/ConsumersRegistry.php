<?php
/**
 * ConsumersRegistry.php
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

use Nette;

use IPub;
use IPub\WebSocketsWAMP\Exceptions;

/**
 * Server push consumers registry
 *
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     PushMessages
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class ConsumersRegistry implements IConsumersRegistry
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var IConsumer[]
	 */
	private $consumers = [];

	public function __construct()
	{
		$this->consumers = [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function addConsumer(IConsumer $consumer)
	{
		$this->consumers[$consumer->getName()] = $consumer;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConsumer(string $name): IConsumer
	{
		if (isset($this->consumers[$name])) {
			return $this->consumers[$name];
		}

		throw new Exceptions\InvalidArgumentException(sprintf('Consumer with name "%s" was not found.', $name));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConsumers(): array
	{
		return $this->consumers;
	}
}
