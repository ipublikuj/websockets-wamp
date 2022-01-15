<?php declare(strict_types = 1);

/**
 * InvalidArgumentException.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:WebSocketsWAMP!
 * @subpackage     Exceptions
 * @since          1.0.0
 *
 * @date           01.03.17
 */

namespace IPub\WebSocketsWAMP\Exceptions;

use IPub\WebSockets\Exceptions;

class InvalidArgumentException extends Exceptions\InvalidArgumentException implements IException
{

}
