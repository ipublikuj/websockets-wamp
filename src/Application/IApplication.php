<?php declare(strict_types = 1);

/**
 * IApplication.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Application
 * @since          1.0.0
 *
 * @date           16.02.17
 */

namespace IPub\WebSocketsWAMP\Application;

use IPub\WebSockets\Application as WebSocketsApplication;
use IPub\WebSocketsWAMP\Entities;

/**
 * WebSockets WAMP application interface
 *
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Application
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IApplication extends WebSocketsApplication\IApplication
{

	/**
	 * @param Entities\PushMessages\IMessage $message
	 * @param string $provider
	 *
	 * @return void
	 */
	public function handlePush(Entities\PushMessages\IMessage $message, string $provider): void;

}
