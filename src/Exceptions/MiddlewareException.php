<?php

namespace Serogaq\TgBotApi\Exceptions;

class MiddlewareException extends TgBotApiException {
    public function __construct(string $message = 'Unknown error', int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
