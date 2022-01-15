<?php declare(strict_types = 1);

/**
 * IDriver.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Topics
 * @since          1.0.0
 *
 * @date           26.02.17
 */

namespace IPub\WebSocketsWAMP\Topics\Drivers;

use IPub\WebSocketsWAMP\Entities;

/**
 * Topics storage driver interface
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Topics
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IDriver
{

	/**
	 * @param string $id
	 *
	 * @return Entities\Topics\ITopic|bool
	 */
	public function fetch(string $id);

	/**
	 * @return Entities\Topics\ITopic[]
	 */
	public function fetchAll(): array;

	/**
	 * @param string $id
	 *
	 * @return bool
	 */
	public function contains(string $id): bool;

	/**
	 * @param string $id
	 * @param mixed $data
	 * @param int $lifeTime
	 *
	 * @return bool True if saved, false otherwise
	 */
	public function save(string $id, $data, int $lifeTime = 0): bool;

	/**
	 * @param string $id
	 *
	 * @return bool true if the cache entry was successfully deleted, false otherwise
	 */
	public function delete(string $id): bool;

}
