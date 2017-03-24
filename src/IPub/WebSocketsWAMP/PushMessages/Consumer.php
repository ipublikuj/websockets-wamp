<?php
/**
 * Consumer.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     PushMessages
 * @since          1.0.0
 *
 * @date           28.02.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\PushMessages;

use Nette;

/**
 * Server push consumer
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     PushMessages
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 *
 * @method onSuccess(IConsumer $consumer, $data = NULL)
 * @method onFail(IConsumer $consumer, $data = NULL)
 */
abstract class Consumer implements IConsumer
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var \Closure
	 */
	public $onSuccess = [];

	/**
	 * @var \Closure
	 */
	public $onFail = [];

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param string $name
	 */
	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
