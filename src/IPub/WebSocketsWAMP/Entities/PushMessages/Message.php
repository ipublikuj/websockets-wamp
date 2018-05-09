<?php
/**
 * Message.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           28.02.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Entities\PushMessages;

use Nette;

/**
 * A push message
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Message implements IMessage
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var string
	 */
	private $topic;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @param string $topic
	 * @param array $data
	 */
	public function __construct(string $topic, array $data)
	{
		$this->topic = $topic;
		$this->data = $data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTopic() : string
	{
		return $this->topic;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getData() : array
	{
		return $this->data;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() : array
	{
		return [
			'topic' => $this->topic,
			'data'  => $this->data,
		];
	}
}
