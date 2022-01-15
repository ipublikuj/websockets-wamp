<?php declare(strict_types = 1);

/**
 * PushMessageSerializer.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Serializers
 * @since          1.0.0
 *
 * @date           28.02.17
 */

namespace IPub\WebSocketsWAMP\Serializers;

use IPub\WebSocketsWAMP\Entities;
use Nette;
use Symfony\Component\Serializer;

/**
 * Push message data serializer
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Serializers
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class PushMessageSerializer
{

	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/** @var Serializer\Serializer */
	private $serializer;

	/** @var Serializer\Normalizer\NormalizerInterface[] */
	private $normalizers;

	/** @var  string */
	private $class;

	/** @var Serializer\Encoder\EncoderInterface[] */
	private $encoders;

	public function __construct()
	{
		$this->normalizers = [
			new Serializer\Normalizer\GetSetMethodNormalizer(),
		];

		$this->encoders = [
			new Serializer\Encoder\JsonEncoder(),
		];

		$this->serializer = new Serializer\Serializer($this->normalizers, $this->encoders);
	}

	/**
	 * @param Entities\PushMessages\IMessage $message
	 *
	 * @return string
	 */
	public function serialize(Entities\PushMessages\IMessage $message): string
	{
		$this->class = get_class($message);

		return $this->serializer->serialize($message, 'json');
	}

	/**
	 * @param string $data
	 *
	 * @return Entities\PushMessages\IMessage
	 */
	public function deserialize(string $data): Entities\PushMessages\IMessage
	{
		$class = $this->class ?? Entities\PushMessages\Message::class;

		return $this->serializer->deserialize($data, $class, 'json');
	}

}
