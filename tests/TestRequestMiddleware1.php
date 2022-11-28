<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Tests;

use Serogaq\TgBotApi\ApiRequest;
use Serogaq\TgBotApi\Interfaces\RequestMiddleware;

class TestRequestMiddleware1 implements RequestMiddleware {
    public function handle(ApiRequest $apiRequest): ApiRequest {
        $arguments = $apiRequest->getArguments();
        $arguments[0]['text'] .= ' World';
        return new ApiRequest($apiRequest->getBotId(), $apiRequest->getMethod(), $arguments);
    }
}
