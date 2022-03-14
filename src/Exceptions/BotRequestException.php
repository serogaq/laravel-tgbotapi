<?php

namespace Serogaq\TgBotApi\Exceptions;

use Illuminate\Http\Client\RequestException;
use Exception;

class BotRequestException extends RequestException {
	
	public function __construct(RequestException $exception) {
        parent::__construct($exception->response);
    }

}
