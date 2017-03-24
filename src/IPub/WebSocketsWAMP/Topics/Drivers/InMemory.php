<?php
/**
 * IDriver.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Topics
 * @since          1.0.0
 *
 * @date           26.02.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Topics\Drivers;

use Nette;

/**
 * Classic memory topic storage driver
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Topics
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class InMemory implements IDriver
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var array
	 */
	private $elements;

	public function __construct()
	{
		$this->elements = [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function fetch(string $id)
	{
		if (!$this->contains($id)) {
			return FALSE;
		}

		return $this->elements[$id];
	}

	/**
	 * {@inheritdoc}
	 */
	public function fetchAll() : array
	{
		return array_values($this->elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function contains(string $id) : bool
	{
		return isset($this->elements[$id]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(string $id, $data, int $lifeTime = 0) : bool
	{
		$this->elements[$id] = $data; // Lifetime is not supported

		return TRUE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete(string $id) : bool
	{
		unset($this->elements[$id]);

		return TRUE;
	}
}
