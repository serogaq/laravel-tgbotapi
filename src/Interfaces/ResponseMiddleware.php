<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Interfaces;

use Serogaq\TgBotApi\ApiResponse;

interface ResponseMiddleware {
    public function handle(ApiResponse $apiResponse): ApiResponse;
}
