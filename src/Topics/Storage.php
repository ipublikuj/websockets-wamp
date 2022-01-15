<?php declare(strict_types = 1);

/**
 * Storage.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Topics
 * @since          1.0.0
 *
 * @date           14.02.17
 */

namespace IPub\WebSocketsWAMP\Topics;

use ArrayIterator;
use IPub\WebSocketsWAMP\Entities;
use IPub\WebSocketsWAMP\Exceptions;
use IPub\WebSocketsWAMP\Topics;
use Nette;
use Psr\Log;
use Throwable;

/**
 * Storage for manage all topics
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Topics
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Storage implements IStorage
{

	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/** @var Topics\Drivers\IDriver */
	private $driver;

	/** @var int|null */
	private $ttl;

	/** @var Log\LoggerInterface|Log\NullLogger|null */
	private $logger;

	/**
	 * @param int|null $ttl
	 * @param Log\LoggerInterface|null $logger
	 */
	public function __construct(int $ttl = 0, ?Log\LoggerInterface $logger = null)
	{
		$this->ttl = $ttl;
		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritdoc}
	 */
	public function setStorageDriver(Topics\Drivers\IDriver $driver): void
	{
		$this->driver = $driver;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getStorageId(Entities\Topics\ITopic $topic): string
	{
		return $topic->getId();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws Exceptions\StorageException
	 * @throws Exceptions\TopicNotFoundException
	 */
	public function getTopic(string $identifier): Entities\Topics\ITopic
	{
		try {
			$result = $this->driver->fetch($identifier);

		} catch (Throwable $ex) {
			throw new Exceptions\StorageException(sprintf('Driver %s failed', self::class), $ex->getCode(), $ex);
		}

		$this->logger->debug('GET TOPIC ' . $identifier);

		if ($result === false) {
			throw new Exceptions\TopicNotFoundException(sprintf('Topic %s not found', $identifier));
		}

		return $result;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws Exceptions\StorageException
	 */
	public function addTopic(string $identifier, Entities\Topics\ITopic $topic): void
	{
		$context = [
			'topic' => $identifier,
		];

		$this->logger->debug(sprintf('INSERT TOPIC ' . $identifier), $context);

		try {
			$result = $this->driver->save($identifier, $topic, $this->ttl);

		} catch (Throwable $ex) {
			throw new Exceptions\StorageException(sprintf('Driver %s failed', self::class), $ex->getCode(), $ex);
		}

		if ($result === false) {
			throw new Exceptions\StorageException('Unable add topic');
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws Exceptions\StorageException
	 */
	public function hasTopic(string $identifier): bool
	{
		try {
			$result = $this->driver->contains($identifier);

		} catch (Throwable $ex) {
			throw new Exceptions\StorageException(sprintf('Driver %s failed', self::class), $ex->getCode(), $ex);
		}

		return $result;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws Exceptions\StorageException
	 */
	public function removeTopic(string $identifier): bool
	{
		$this->logger->debug('REMOVE TOPIC ' . $identifier);

		try {
			$result = $this->driver->delete($identifier);

		} catch (Throwable $ex) {
			throw new Exceptions\StorageException(sprintf('Driver %s failed', self::class), $ex->getCode(), $ex);
		}

		return $result;
	}

	/**
	 * @return Entities\Topics\ITopic[]|ArrayIterator
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->driver->fetchAll());
	}

	/**
	 * @param Log\LoggerInterface $logger
	 *
	 * @return void
	 */
	public function setLogger(Log\LoggerInterface $logger): void
	{
		$this->logger = $logger;
	}

}
