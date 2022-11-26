<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Services;

use Illuminate\Support\Facades\App;
use Serogaq\TgBotApi\Exceptions\MiddlewareException;
use Serogaq\TgBotApi\Interfaces\{ RequestMiddleware, ResponseMiddleware };
use Serogaq\TgBotApi\{ ApiRequest, ApiResponse };

class Middleware {
    protected array $requestMiddleware = [];

    protected array $responseMiddleware = [];

    public function addRequestMiddleware(string $middleware): void {
        if ($this->isAlreadyAdded($middleware)) {
            return;
        }
        if (!is_subclass_of($middleware, RequestMiddleware::class)) {
            return;
        }
        $this->requestMiddleware[$middleware] = $middleware;
    }

    public function addResponseMiddleware(string $middleware): void {
        if ($this->isAlreadyAdded($middleware)) {
            return;
        }
        if (!is_subclass_of($middleware, ResponseMiddleware::class)) {
            return;
        }
        $this->responseMiddleware[$middleware] = $middleware;
    }

    public function flushAll(): void {
        $this->requestMiddleware = [];
        $this->responseMiddleware = [];
    }

    protected function isAlreadyAdded(string $middleware): bool {
        if (is_subclass_of($middleware, RequestMiddleware::class)) {
            return isset($this->requestMiddleware[$middleware]) ? true : false;
        } elseif (is_subclass_of($middleware, ResponseMiddleware::class)) {
            return isset($this->responseMiddleware[$middleware]) ? true : false;
        }
        throw new MiddlewareException('Middleware should implement \Serogaq\TgBotApi\Interfaces\RequestMiddleware or \Serogaq\TgBotApi\Interfaces\ResponseMiddleware');
    }

    public function execRequestMiddlewares(ApiRequest $apiRequest): ApiRequest {
        if (empty($this->requestMiddleware)) {
            return $apiRequest;
        }
        foreach ($this->requestMiddleware as $middleware) {
            $middleware = App::make($middleware);
            $apiRequest = $middleware->handle($apiRequest);
        }
        $this->flushAll();
        return $apiRequest;
    }

    public function execResponseMiddlewares(ApiResponse $apiResponse): ApiResponse {
        if (empty($this->responseMiddleware)) {
            return $apiResponse;
        }
        foreach ($this->responseMiddleware as $middleware) {
            $middleware = App::make($middleware);
            $apiResponse = $middleware->handle($apiResponse);
        }
        $this->flushAll();
        return $apiResponse;
    }
}
