<?php
/**
 * InvalidArgumentException.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:WebSocketWAMP!
 * @subpackage     Exceptions
 * @since          1.0.0
 *
 * @date           01.03.17
 */

declare(strict_types = 1);

namespace IPub\WebSocketsWAMP\Exceptions;

use IPub;
use IPub\WebSockets\Exceptions;

class InvalidArgumentException extends Exceptions\InvalidArgumentException implements IException
{
}
