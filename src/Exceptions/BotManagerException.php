<?php

namespace Serogaq\TgBotApi\Exceptions;

use Exception;

class BotManagerException extends Exception {
	
	public function __construct(string $message, int $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
