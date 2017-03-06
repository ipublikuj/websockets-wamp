<?php
/**
 * Pusher.php
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
use IPub\WebSocketsWAMP\Entities;
use IPub\WebSocketsWAMP\Serializers;

use IPub\WebSockets\Router as WebSocketsRouter;

/**
 * Server message pusher
 *
 * @package        iPublikuj:WebSocketWAMP!
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
	 */
	public function push($data, $destination, array $routeParameters = [], array $context = [])
	{
		$channel = $this->linkGenerator->link($destination, $routeParameters);

		$message = new Entities\PushMessages\Message($channel, $data);

		return $this->doPush($this->serializer->serialize($message), $context);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setConnected(bool $bool = TRUE)
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
	 * @return string
	 */
	abstract protected function doPush(string $data, array $context = []);
}
