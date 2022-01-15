<?php declare(strict_types = 1);

/**
 * Application.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Application
 * @since          1.0.0
 *
 * @date           14.02.17
 */

namespace IPub\WebSocketsWAMP\Application;

use Closure;
use IPub\WebSockets\Application as WebSocketsApplication;
use IPub\WebSockets\Clients as WebSocketsClients;
use IPub\WebSockets\Entities as WebSocketsEntities;
use IPub\WebSockets\Exceptions as WebSocketsExceptions;
use IPub\WebSockets\Http as WebSocketsHttp;
use IPub\WebSockets\Router as WebSocketsRouter;
use IPub\WebSockets\Server as WebSocketsServer;
use IPub\WebSocketsWAMP\Entities;
use IPub\WebSocketsWAMP\Exceptions;
use IPub\WebSocketsWAMP\Topics;
use Nette\Http;
use Nette\Utils;
use Psr\Log;
use SplObjectStorage;
use Throwable;

/**
 * Application which run on server and provide creating controllers
 * with correctly params - convert message => control
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Application
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 *
 * @method onPush(Entities\PushMessages\IMessage $message, string $provider, Entities\Topics\ITopic $topic)
 */
final class Application extends WebSocketsApplication\Application implements IApplication
{

	public const MSG_WELCOME = 0;
	public const MSG_PREFIX = 1;
	public const MSG_CALL = 2;
	public const MSG_CALL_RESULT = 3;
	public const MSG_CALL_ERROR = 4;
	public const MSG_SUBSCRIBE = 5;
	public const MSG_UNSUBSCRIBE = 6;
	public const MSG_PUBLISH = 7;
	public const MSG_EVENT = 8;

	/** @var Closure */
	public $onPush = [];

	/** @var SplObjectStorage */
	private $subscriptions;

	/** @var Topics\IStorage */
	private $topicsStorage;

	/**
	 * @param Topics\IStorage $topicsStorage
	 * @param WebSocketsRouter\IRouter $router
	 * @param WebSocketsApplication\Controller\IControllerFactory $controllerFactory
	 * @param WebSocketsClients\IStorage $clientsStorage
	 * @param Log\LoggerInterface|null $logger
	 */
	public function __construct(
		Topics\IStorage $topicsStorage,
		WebSocketsRouter\IRouter $router,
		WebSocketsApplication\Controller\IControllerFactory $controllerFactory,
		WebSocketsClients\IStorage $clientsStorage,
		?Log\LoggerInterface $logger = null
	) {
		parent::__construct($router, $controllerFactory, $clientsStorage, $logger);

		$this->topicsStorage = $topicsStorage;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws Utils\JsonException
	 */
	public function handleOpen(WebSocketsEntities\Clients\IClient $client, WebSocketsHttp\IRequest $httpRequest): void
	{
		$client->addParameter('wampSession', str_replace('.', '', uniqid((string) mt_rand(), true)));

		// Send welcome handshake
		$client->send(Utils\Json::encode([
			self::MSG_WELCOME,
			$client->getParameter('wampSession'),
			1,
			WebSocketsServer\Server::VERSION,
		]));

		$this->subscriptions = new SplObjectStorage();

		parent::handleOpen($client, $httpRequest);
	}

	/**
	 * {@inheritdoc}
	 */
	public function handleClose(WebSocketsEntities\Clients\IClient $client, WebSocketsHttp\IRequest $httpRequest): void
	{
		parent::handleClose($client, $httpRequest);

		foreach ($this->topicsStorage as $topic) {
			$this->cleanTopic($topic, $client);
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws WebSocketsExceptions\TerminateException
	 */
	public function handleMessage(
		WebSocketsEntities\Clients\IClient $client,
		WebSocketsHttp\IRequest $httpRequest,
		string $message
	): void {
		parent::handleMessage($client, $httpRequest, $message);

		try {
			$json = Utils\Json::decode($message, Utils\Json::FORCE_ARRAY);

			if ($json === null || !is_array($json) || $json !== array_values($json)) {
				throw new Exceptions\InvalidArgumentException('Invalid WAMP message format');
			}

			switch ($json[0]) {
				case self::MSG_PREFIX:
					$prefixes = $client->getParameter('prefixes', []);
					$prefixes[$json[1]] = $json[2];

					$client->addParameter('prefixes', $prefixes);

					$client->send(Utils\Json::encode([self::MSG_PREFIX, $json[1], (string) $json[2]]));
					break;

				// RPC action
				case self::MSG_CALL:
					array_shift($json);

					$rpcId = array_shift($json);
					$topicId = array_shift($json);

					$topic = $this->getTopic($topicId);

					if (count($json) === 1 && is_array($json[0])) {
						$json = $json[0];
					}

					$httpRequest = $this->modifyRequest($httpRequest, $topic, 'call');

					try {
						$response = $this->processMessage($httpRequest, [
							'client' => $client,
							'topic'  => $topic,
							'rpcId'  => $rpcId,
							'args'   => $json,
						]);

						$client->send(Utils\Json::encode([self::MSG_CALL_RESULT, $rpcId, $response->create()]));

					} catch (WebSocketsExceptions\TerminateException $ex) {
						throw $ex;

					} catch (Throwable $ex) {
						$data = [
							self::MSG_CALL_ERROR,
							$rpcId,
							$topicId,
							$ex->getMessage(),
							[
								'code'   => $ex->getCode(),
								'params' => $json,
							],
						];

						$client->send(Utils\Json::encode($data));
					}

					$this->logger->info(sprintf('Connection %s has called RPC on %s topic', $client->getId(), $topic->getId()));
					break;

				// Subscribe to topic
				case self::MSG_SUBSCRIBE:
					$topic = $this->getTopic($json[1]);

					$subscribedTopics = $client->getParameter('subscribedTopics', new SplObjectStorage());

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
				case self::MSG_UNSUBSCRIBE:
					$topic = $this->getTopic($json[1]);

					$subscribedTopics = $client->getParameter('subscribedTopics', new SplObjectStorage());

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
				case self::MSG_PUBLISH:
					$topic = $this->getTopic($json[1]);

					$exclude = (array_key_exists(3, $json) ? $json[3] : null);

					if (!is_array($exclude)) {
						if ((bool) $exclude === true) {
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
		} catch (WebSocketsExceptions\TerminateException $ex) {
			throw $ex;

		} catch (Throwable $ex) {
			$this->logger->error(sprintf('An error (%s) has occurred: %s', $ex->getCode(), $ex->getMessage()));

			$client->close(1007);
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws WebSocketsExceptions\TerminateException
	 */
	public function handlePush(Entities\PushMessages\IMessage $message, string $provider): void
	{
		try {
			$topic = $this->getTopic($message->getTopic());

			$url = new Http\Url($message->getTopic());
			$action = $url->getQueryParameter(WebSocketsApplication\Controller\Controller::ACTION_KEY);

			if ($action === null || $action === WebSocketsApplication\Controller\Controller::DEFAULT_ACTION) {
				$url->setQueryParameter(WebSocketsApplication\Controller\Controller::ACTION_KEY, 'push');
			}

			$httpRequest = new WebSocketsHttp\Request(new Http\UrlScript($url), null, null, null, null, null, WebSocketsHttp\IRequest::GET);

			$this->processMessage($httpRequest, [
				'topic'   => $topic,
				'data'    => $message->getData(),
				'message' => $message,
			]);

			$this->logger->info(sprintf('Message was pushed to %s topic', $topic->getId()));

			$this->onPush($message, $provider, $topic);

		} catch (WebSocketsExceptions\TerminateException $ex) {
			throw $ex;

		} catch (Throwable $ex) {
			$context = [
				'provider' => $provider,
				'topic'    => $message->getTopic(),
				'data'     => $message->getData(),
			];

			$this->logger->error(sprintf('An error (%s) has occurred: %s', $ex->getCode(), $ex->getMessage()), $context);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSubProtocols(): array
	{
		return ['wamp'];
	}

	/**
	 * @param string $topic
	 *
	 * @return Entities\Topics\ITopic
	 */
	private function getTopic(string $topic): Entities\Topics\ITopic
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
	private function cleanTopic(Entities\Topics\ITopic $topic, WebSocketsEntities\Clients\IClient $client): void
	{
		$subscribedTopics = $client->getParameter('subscribedTopics', new SplObjectStorage());

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
	private function modifyRequest(
		WebSocketsHttp\IRequest $httpRequest,
		Entities\Topics\ITopic $topic,
		string $action
	): WebSocketsHttp\IRequest {
		$url = new Http\Url((string) $httpRequest->getUrl());
		$url->setPath(rtrim($url->getPath(), '/') . '/' . ltrim($topic->getId(), '/'));

		$parsedAction = $url->getQueryParameter(WebSocketsApplication\Controller\Controller::ACTION_KEY);

		if ($parsedAction === null || $parsedAction === WebSocketsApplication\Controller\Controller::DEFAULT_ACTION) {
			$url->setQueryParameter(WebSocketsApplication\Controller\Controller::ACTION_KEY, $action);
		}

		$httpRequest->setUrl(new Http\UrlScript($url));

		return $httpRequest;
	}

}
