<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Services;

use Serogaq\TgBotApi\Interfaces\{ RequestMiddleware, ResponseMiddleware };
use Serogaq\TgBotApi\{ ApiRequest, ApiResponse };
use Illuminate\Support\Facades\App;

class Middleware {

    protected array $requestMiddleware = [];

    protected array $responseMiddleware = [];

    public function addRequestMiddleware(string $middleware): void {
        if($this->isAlreadyAdded($middleware)) return;
        $implements = class_implements($middleware);
        reset($implements);
        $implements = key($implements);
        if ($implements !== RequestMiddleware::class) return;
        $this->requestMiddleware[$middleware] = $middleware;
    }

    public function addResponseMiddleware(string $middleware): void {
        if($this->isAlreadyAdded($middleware)) return;
        $implements = class_implements($middleware);
        reset($implements);
        $implements = key($implements);
        if ($implements !== ResponseMiddleware::class) return;
        $this->responseMiddleware[$middleware] = $middleware;
    }

    public function flushAll(): void {
        $this->requestMiddleware = [];
        $this->responseMiddleware = [];
    }

    protected function isAlreadyAdded(string $middleware): bool {
        $implements = class_implements($middleware);
        reset($implements);
        $implements = key($implements);
        if ($implements === RequestMiddleware::class) {
            return isset($this->requestMiddleware[$middleware]) ? true : false;
        } elseif ($implements === ResponseMiddleware::class) {
            return isset($this->responseMiddleware[$middleware]) ? true : false;
        }
        // TODO: Exception: middleware should implement RequestMiddleware|ResponseMiddleware
    }

    public function execRequestMiddlewares(ApiRequest $apiRequest): ApiRequest {
        if(empty($this->requestMiddleware)) return $apiRequest;
        foreach ($this->requestMiddleware as $middleware) {
            $middleware = App::make($middleware);
            $apiRequest = $middleware->handle($apiRequest);
        }
        $this->flushAll();
        return $apiRequest;
    }

    public function execResponseMiddlewares(ApiResponse $apiResponse): ApiResponse {
        if(empty($this->responseMiddleware)) return $apiResponse;
        foreach ($this->responseMiddleware as $middleware) {
            $middleware = App::make($middleware);
            $apiResponse = $middleware->handle($apiResponse);
        }
        $this->flushAll();
        return $apiResponse;
    }

}