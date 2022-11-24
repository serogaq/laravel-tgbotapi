<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Middleware;

use Serogaq\TgBotApi\Interfaces\ResponseMiddleware;
use Serogaq\TgBotApi\ApiResponse;

class Response implements ResponseMiddleware {

    public function handle(ApiResponse $apiResponse): ApiResponse {
        return $apiResponse;
    }

}