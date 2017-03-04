<?php
/**
 * ServerEvent.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     Events
 * @since          1.0.0
 *
 * @date           01.03.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Events;

use Nette;

final class OnServerStartHandler
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;
}
