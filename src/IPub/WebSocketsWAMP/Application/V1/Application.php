<?php
/**
 * Application.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     Application
 * @since          1.0.0
 *
 * @date           14.02.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Application\V1;

use Nette\Http;
use Nette\Utils;

use Psr\Log;

use IPub;
use IPub\WebSocketsWAMP\Entities;
use IPub\WebSocketsWAMP\Exceptions;
use IPub\WebSocketsWAMP\Topics;

use IPub\WebSockets\Application as WebSocketsApplication;
use IPub\WebSockets\Clients as WebSocketsClients;
use IPub\WebSockets\Entities as WebSocketsEntities;
use IPub\WebSockets\Http as WebSocketsHttp;
use IPub\WebSockets\Router as WebSocketsRouter;
use IPub\WebSockets\Server as WebSocketsServer;

/**
 * Application which run on server and provide creating controllers
 * with correctly params - convert message => control
 *
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     Application
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Application extends WebSocketsApplication\Application implements IApplication
{
	const MSG_WELCOME = 0;
	const MSG_PREFIX = 1;
	const MSG_CALL = 2;
	const MSG_CALL_RESULT = 3;
	const MSG_CALL_ERROR = 4;
	const MSG_SUBSCRIBE = 5;
	const MSG_UNSUBSCRIBE = 6;
	const MSG_PUBLISH = 7;
	const MSG_EVENT = 8;

	/**
	 * @var \SplObjectStorage
	 */
	private $subscriptions;

	/**
	 * @var Topics\IStorage
	 */
	private $topicsStorage;

	/**
	 * @param Topics\IStorage $topicsStorage
	 * @param WebSocketsRouter\IRouter $router
	 * @param WebSocketsApplication\Controller\IControllerFactory $controllerFactory
	 * @param WebSocketsClients\IStorage $clientsStorage
	 * @param Log\LoggerInterface|NULL $logger
	 */
	public function __construct(
		Topics\IStorage $topicsStorage,
		WebSocketsRouter\IRouter $router,
		WebSocketsApplication\Controller\IControllerFactory $controllerFactory,
		WebSocketsClients\IStorage $clientsStorage,
		Log\LoggerInterface $logger = NULL
	) {
		parent::__construct($router, $controllerFactory, $clientsStorage, $logger);

		$this->topicsStorage = $topicsStorage;
	}

	/**
	 * {@inheritdoc}
	 */
	public function onOpen(WebSocketsEntities\Clients\IClient $client, WebSocketsHttp\IRequest $httpRequest)
	{
		$client->addParameter('wampSession', str_replace('.', '', uniqid((string) mt_rand(), TRUE)));

		// Send welcome handshake
		$client->send(Utils\Json::encode([
			self::MSG_WELCOME,
			$client->getParameter('wampSession'),
			1,
			WebSocketsServer\Server::VERSION,
		]));

		$this->subscriptions = new \SplObjectStorage;

		parent::onOpen($client, $httpRequest);
	}

	/**
	 * {@inheritdoc}
	 */
	public function onClose(WebSocketsEntities\Clients\IClient $client, WebSocketsHttp\IRequest $httpRequest)
	{
		parent::onClose($client, $httpRequest);

		foreach ($this->topicsStorage as $topic) {
			$this->cleanTopic($topic, $client);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function onMessage(WebSocketsEntities\Clients\IClient $client, WebSocketsHttp\IRequest $httpRequest, string $message)
	{
		try {
			$json = Utils\Json::decode($message);

			if ($json === NULL || !is_array($json) || $json !== array_values($json)) {
				throw new Exceptions\InvalidArgumentException('Invalid WAMP message format');
			}

			switch ($json[0]) {
				case static::MSG_PREFIX:
					$prefixes = $client->getParameter('prefixes', []);
					$prefixes[$json[1]] = $json[2];

					$client->addParameter('prefixes', $prefixes);

					$client->send(Utils\Json::encode([self::MSG_PREFIX, $json[1], (string) $json[2]]));
					break;

				// RPC action
				case static::MSG_CALL:
					array_shift($json);

					$rpcId = array_shift($json);
					$topic = array_shift($json);

					if (count($json) === 1 && is_array($json[0])) {
						$json = $json[0];
					}

					$httpRequest = $this->modifyRequest($httpRequest, $this->getTopic($topic), 'call');

					try {
						$response = $this->processMessage($httpRequest, [
							'client' => $client,
							'rpcId'  => $rpcId,
							'args'   => $json,
						]);

						$client->send(Utils\Json::encode([self::MSG_CALL_RESULT, $rpcId, $response]));

					} catch (\Exception $ex) {
						$data = [self::MSG_CALL_ERROR, $rpcId, $topic, $ex->getMessage(), [
							'code'   => $ex->getCode(),
							'rpc'    => $topic,
							'params' => $json,
						]];

						$client->send(Utils\Json::encode($data));
					}
					break;

				// Subscribe to topic
				case static::MSG_SUBSCRIBE:
					$topic = $this->getTopic($json[1]);

					$subscribedTopics = $client->getParameter('subscribedTopics', new \SplObjectStorage());

					if ($subscribedTopics->contains($topic)) {
						return;
					}

					$topic = $this->topicsStorage->getTopic($topic->getId());
					$topic->add($client);

					$this->topicsStorage->addTopic($topic->getId(), $topic);

					$subscribedTopics->attach($topic);

					$client->addParameter('subscribedTopics', $subscribedTopics);

					$httpRequest = $this->modifyRequest($httpRequest, $topic, 'subscribe');

					$this->processMessage($httpRequest, [
						'client' => $client,
						'topic'  => $topic,
					]);

					$this->logger->info(sprintf('Connection %s has subscribed to %s', $client->getId(), $topic->getId()));
					break;

				// Unsubscribe from topic
				case static::MSG_UNSUBSCRIBE:
					$topic = $this->getTopic($json[1]);

					$subscribedTopics = $client->getParameter('subscribedTopics', new \SplObjectStorage());

					if (!$subscribedTopics->contains($topic)) {
						return;
					}

					$this->cleanTopic($topic, $client);

					$httpRequest = $this->modifyRequest($httpRequest, $topic, 'unsubscribe');

					$this->processMessage($httpRequest, [
						'client' => $client,
						'topic'  => $topic,
					]);

					$this->logger->info(sprintf('Connection %s has unsubscribed from %s', $client->getId(), $topic->getId()));
					break;

				// Publish to topic
				case static::MSG_PUBLISH:
					$topic = $this->getTopic($json[1]);

					$exclude = (array_key_exists(3, $json) ? $json[3] : NULL);

					if (!is_array($exclude)) {
						if ((bool) $exclude === TRUE) {
							$exclude = [$client->getParameter('wampSession')];

						} else {
							$exclude = [];
						}
					}

					$eligible = (array_key_exists(4, $json) ? $json[4] : []);

					$event = $json[2];

					$httpRequest = $this->modifyRequest($httpRequest, $topic, 'publish');

					$this->processMessage($httpRequest, [
						'client'   => $client,
						'topic'    => $topic,
						'event'    => $event,
						'exclude'  => $exclude,
						'eligible' => $eligible,
					]);

					$this->logger->info(sprintf('Connection %s has published to %s topic', $client->getId(), $topic->getId()));
					break;

				default:
					throw new Exceptions\InvalidArgumentException('Invalid WAMP message type');
			}

		} catch (\Exception $ex) {
			$this->logger->error(sprintf('An error (%s) has occurred: %s', $ex->getCode(), $ex->getMessage()));

			$client->close(1007);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function onPush(Entities\PushMessages\IMessage $message, string $provider)
	{
		try {
			$topic = $this->getTopic($message->getTopic());

			$url = new Http\UrlScript();
			$url->setScheme('ws');
			$url->setHost($this->serverConfiguration->getHttpHost());
			$url->setPort($this->serverConfiguration->getPort());
			$url->setPath($message->getTopic());

			$httpRequest = new WebSocketsHttp\Request($url, NULL, NULL, NULL, NULL, NULL, WebSocketsHttp\IRequest::POST);

			$httpRequest = $this->modifyRequest($httpRequest, $topic, 'push');

			$this->processMessage($httpRequest, [
				'topic'  => $topic,
				'data'   => $message->getData(),
			]);

		} catch (\Exception $ex) {

		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSubProtocols() : array
	{
		return ['wamp'];
	}

	/**
	 * @param string $topic
	 *
	 * @return Entities\Topics\ITopic
	 */
	private function getTopic(string $topic) : Entities\Topics\ITopic
	{
		if (!$this->topicsStorage->hasTopic($topic)) {
			$this->topicsStorage->addTopic($topic, new Entities\Topics\Topic($topic));
		}

		return $this->topicsStorage->getTopic($topic);
	}

	/**
	 * @param Entities\Topics\ITopic $topic
	 * @param WebSocketsEntities\Clients\IClient $client
	 *
	 * @return void
	 */
	private function cleanTopic(Entities\Topics\ITopic $topic, WebSocketsEntities\Clients\IClient $client)
	{
		$subscribedTopics = $client->getParameter('subscribedTopics', new \SplObjectStorage());

		if ($subscribedTopics->contains($topic)) {
			$subscribedTopics->detach($topic);
		}

		$topic = $this->topicsStorage->getTopic($topic->getId());
		$topic->remove($client);

		$this->topicsStorage->addTopic($topic->getId(), $topic);

		if ($topic->isAutoDeleteEnabled() && $topic->count() === 0) {
			$this->topicsStorage->removeTopic($topic->getId());
		}
	}

	/**
	 * @param WebSocketsHttp\IRequest $httpRequest
	 * @param Entities\Topics\ITopic $topic
	 * @param string $action
	 *
	 * @return WebSocketsHttp\IRequest
	 */
	private function modifyRequest(WebSocketsHttp\IRequest $httpRequest, Entities\Topics\ITopic $topic, string $action) : WebSocketsHttp\IRequest
	{
		$url = $httpRequest->getUrl();
		$url->setPath(rtrim($url->getPath(), '/') . '/' . ltrim($topic->getId(), '/'));

		$url->setQueryParameter('action', $action);

		$httpRequest->setUrl($url);

		return $httpRequest;
	}
}
