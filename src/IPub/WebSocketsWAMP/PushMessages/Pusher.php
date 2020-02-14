<?php
/**
 * Pusher.php
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

use ReflectionException;

use Nette;

use IPub\WebSocketsWAMP\Entities;
use IPub\WebSocketsWAMP\Serializers;

use IPub\WebSockets\Exceptions as WebSocketsExceptions;
use IPub\WebSockets\Router as WebSocketsRouter;

/**
 * Server message pusher
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     PushMessages
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
abstract class Pusher implements IPusher
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var Serializers\PushMessageSerializer
	 */
	private $serializer;

	/**
	 * @var WebSocketsRouter\LinkGenerator
	 */
	private $linkGenerator;

	/**
	 * @var  bool
	 */
	private $connected = FALSE;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param string $name
	 * @param Serializers\PushMessageSerializer $serializer
	 * @param WebSocketsRouter\LinkGenerator $linkGenerator
	 */
	public function __construct(
		string $name,
		Serializers\PushMessageSerializer $serializer,
		WebSocketsRouter\LinkGenerator $linkGenerator
	) {
		$this->name = $name;
		$this->serializer = $serializer;
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws WebSocketsExceptions\InvalidLinkException
	 * @throws ReflectionException
	 */
	public function push($data, $destination, array $routeParameters = [], array $context = []) : void
	{
		$channel = $this->linkGenerator->link($destination, $routeParameters);

		$message = new Entities\PushMessages\Message($channel, $data);

		$this->doPush($this->serializer->serialize($message), $context);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setConnected(bool $bool = TRUE) : void
	{
		$this->connected = $bool;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isConnected() : bool
	{
		return $this->connected;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * @param string $data
	 * @param array $context
	 *
	 * @return void
	 */
	abstract protected function doPush(string $data, array $context = []) : void;
}
