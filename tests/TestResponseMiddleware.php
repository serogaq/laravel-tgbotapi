<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Tests;

use Serogaq\TgBotApi\ApiResponse;
use Serogaq\TgBotApi\Interfaces\ResponseMiddleware;

class TestResponseMiddleware implements ResponseMiddleware {
    public function handle(ApiResponse $apiResponse): ApiResponse {
        $response = $apiResponse->asArray();
        $response['test'] = true;
        return new ApiResponse($response, $apiResponse->getStatusCode(), $apiResponse->getRequestId());
    }
}
