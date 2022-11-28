<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Tests;

use Serogaq\TgBotApi\Interfaces\RequestMiddleware;
use Serogaq\TgBotApi\ApiRequest;

class TestRequestMiddleware2 implements RequestMiddleware {

    public function handle(ApiRequest $apiRequest): ApiRequest {
        $arguments = $apiRequest->getArguments();
        $arguments[0]['new_text'] = $arguments[0]['text'] . '!';
        return new ApiRequest($apiRequest->getBotId(), $apiRequest->getMethod() . 'Test', $arguments);
    }

}
