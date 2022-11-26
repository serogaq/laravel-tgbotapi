<?php

namespace Serogaq\TgBotApi\Exceptions;

use Serogaq\TgBotApi\Interfaces\TgBotApiException as ITgBotApiException;

abstract class TgBotApiException extends \RuntimeException implements ITgBotApiException {
}
