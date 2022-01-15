<?php declare(strict_types = 1);

/**
 * IStorage.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Topics
 * @since          1.0.0
 *
 * @date           24.02.17
 */

namespace IPub\WebSocketsWAMP\Topics;

use IPub\WebSocketsWAMP\Entities;
use IPub\WebSocketsWAMP\Topics;
use IteratorAggregate;

/**
 * Storage for manage all topics
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Topics
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IStorage extends IteratorAggregate
{

	/**
	 * @param Topics\Drivers\IDriver $driver
	 *
	 * @return void
	 */
	public function setStorageDriver(Topics\Drivers\IDriver $driver): void;

	/**
	 * @param Entities\Topics\ITopic $topic
	 *
	 * @return string
	 */
	public static function getStorageId(Entities\Topics\ITopic $topic): string;

	/**
	 * @param string $identifier
	 *
	 * @return Entities\Topics\ITopic
	 */
	public function getTopic(string $identifier): Entities\Topics\ITopic;

	/**
	 * @param string $identifier
	 * @param Entities\Topics\ITopic $topic
	 *
	 * @return void
	 */
	public function addTopic(string $identifier, Entities\Topics\ITopic $topic): void;

	/**
	 * @param string $identifier
	 *
	 * @return bool
	 */
	public function hasTopic(string $identifier): bool;

	/**
	 * @param string $identifier
	 *
	 * @return bool
	 */
	public function removeTopic(string $identifier): bool;

}
