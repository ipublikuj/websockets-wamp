<?php
/**
 * IDriver.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     Topics
 * @since          1.0.0
 *
 * @date           26.02.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Topics\Drivers;

use IPub;
use IPub\WebSocketsWAMP\Entities;

/**
 * Topics storage driver interface
 *
 * @package        iPublikuj:WebSocketWAMP!
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
	function fetch(string $id);

	/**
	 * @return Entities\Topics\ITopic[]
	 */
	function fetchAll() : array;

	/**
	 * @param string $id
	 *
	 * @return bool
	 */
	function contains(string $id) : bool;

	/**
	 * @param string $id
	 * @param mixed $data
	 * @param int $lifeTime
	 *
	 * @return bool True if saved, false otherwise
	 */
	function save(string $id, $data, int $lifeTime = 0) : bool;

	/**
	 * @param string $id
	 *
	 * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise
	 */
	function delete(string $id) : bool;
}
