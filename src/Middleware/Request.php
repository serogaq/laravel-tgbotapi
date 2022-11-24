<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Middleware;

use Serogaq\TgBotApi\Interfaces\RequestMiddleware;
use Serogaq\TgBotApi\ApiRequest;

class Request implements RequestMiddleware {

    public function handle(ApiRequest $apiRequest): ApiRequest {
        return $apiRequest;
    }

}