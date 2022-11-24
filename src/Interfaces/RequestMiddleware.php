<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Interfaces;

use Serogaq\TgBotApi\ApiRequest;

interface RequestMiddleware {

    public function handle(ApiRequest $apiRequest): ApiRequest;

}
