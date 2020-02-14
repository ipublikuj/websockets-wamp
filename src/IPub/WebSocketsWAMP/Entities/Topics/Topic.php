<?php
/**
 * Topic.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           25.02.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Entities\Topics;

use Nette;
use Nette\Utils;

use IPub\WebSocketsWAMP\Application;
use IPub\WebSocketsWAMP\Exceptions;

use IPub\WebSockets\Application as WebSocketsApplication;
use IPub\WebSockets\Entities as WebSocketsEntities;

/**
 * A topic/channel containing connections that have subscribed to it
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Topic implements ITopic
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * If true the TopicManager will destroy this object if it's ever empty of connections
	 *
	 * @type bool
	 */
	private $autoDelete = FALSE;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var \SplObjectStorage
	 */
	private $subscribers;

	/**
	 * @param string $topicId Unique ID for this object
	 */
	public function __construct(string $topicId)
	{
		$this->id = $topicId;
		$this->subscribers = new \SplObjectStorage;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId() : string
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws Exceptions\InvalidArgumentException
	 * @throws Utils\JsonException
	 */
	public function broadcast($message, array $exclude = [], array $eligible = []) : void
	{
		if (!is_string($message) && !$message instanceof WebSocketsApplication\Responses\IResponse) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided message for broadcasting have to be string or instance of "%s"', WebSocketsApplication\Responses\IResponse::class));
		}

		$useEligible = (bool) count($eligible);

		/** @var WebSocketsEntities\Clients\IClient $client */
		foreach ($this->subscribers as $client) {
			if (in_array($client->getId(), $exclude)) {
				continue;
			}

			if ($useEligible && !in_array($client->getParameter('subscribedTopics'), $eligible)) {
				continue;
			}

			$client->send(Utils\Json::encode([Application\Application::MSG_EVENT, $this->id, (string) $message]));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function has(WebSocketsEntities\Clients\IClient $client) : bool
	{
		return $this->subscribers->contains($client);
	}

	/**
	 * {@inheritdoc}
	 */
	public function add(WebSocketsEntities\Clients\IClient $client) : void
	{
		$this->subscribers->attach($client);
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove(WebSocketsEntities\Clients\IClient $client) : void
	{
		if ($this->subscribers->contains($client)) {
			$this->subscribers->detach($client);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator()
	{
		return $this->subscribers;
	}

	/**
	 * {@inheritdoc}
	 */
	public function count() : int
	{
		return $this->subscribers->count();
	}

	/**
	 * {@inheritdoc}
	 */
	public function enableAutoDelete() : void
	{
		$this->autoDelete = TRUE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function disableAutoDelete() : void
	{
		$this->autoDelete = FALSE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAutoDeleteEnabled() : bool
	{
		return $this->autoDelete;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString()
	{
		return $this->getId();
	}
}
