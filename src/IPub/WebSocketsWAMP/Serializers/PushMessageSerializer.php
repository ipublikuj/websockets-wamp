<?php
/**
 * PushMessageSerializer.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     Serializers
 * @since          1.0.0
 *
 * @date           28.02.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Serializers;

use Nette;

use Symfony\Component\Serializer;

use IPub;
use IPub\WebSocketsWAMP\Entities;

/**
 * Push message data serializer
 *
 * @package        iPublikuj:WebSocketWAMP!
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

	/**
	 * @var Serializer\Serializer
	 */
	private $serializer;

	/**
	 * @var Serializer\Normalizer\NormalizerInterface[]
	 */
	private $normalizers;

	/**
	 * @var  string
	 */
	private $class;

	/**
	 * @var Serializer\Encoder\EncoderInterface[]
	 */
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
	public function serialize(Entities\PushMessages\IMessage $message) : string
	{
		$this->class = get_class($message);

		return $this->serializer->serialize($message, 'json');
	}

	/**
	 * @param string $data
	 *
	 * @return Entities\PushMessages\IMessage
	 */
	public function deserialize(string $data) : Entities\PushMessages\IMessage
	{
		$class = $this->class === NULL ? Entities\PushMessages\Message::class : $this->class;

		return $this->serializer->deserialize($data, $class, 'json');
	}
}
