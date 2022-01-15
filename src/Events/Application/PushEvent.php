<?php declare(strict_types = 1);

/**
 * PushEvent.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Events
 * @since          1.0.0
 *
 * @date           15.11.19
 */

namespace IPub\WebSocketsWAMP\Events\Application;

use IPub\WebSocketsWAMP\Entities;
use Symfony\Contracts\EventDispatcher;

/**
 * Message pushed into topic event
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Events
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class PushEvent extends EventDispatcher\Event
{

	/** @var Entities\PushMessages\IMessage */
	private $message;

	/** @var string */
	private $provider;

	/** @var Entities\Topics\ITopic */
	private $topic;

	/**
	 * @param Entities\PushMessages\IMessage $message
	 * @param string $provider
	 * @param Entities\Topics\ITopic $topic
	 */
	public function __construct(
		Entities\PushMessages\IMessage $message,
		string $provider,
		Entities\Topics\ITopic $topic
	) {
		$this->message = $message;
		$this->provider = $provider;
		$this->topic = $topic;
	}

	/**
	 * @return Entities\PushMessages\IMessage
	 */
	public function getMessage(): Entities\PushMessages\IMessage
	{
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getProvider(): string
	{
		return $this->provider;
	}

	/**
	 * @return Entities\Topics\ITopic
	 */
	public function getTopic(): Entities\Topics\ITopic
	{
		return $this->topic;
	}

}
