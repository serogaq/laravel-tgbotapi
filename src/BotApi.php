<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi;

use Serogaq\TgBotApi\Exceptions\ApiClientException;
use Serogaq\TgBotApi\Interfaces\HttpClient;
use Serogaq\TgBotApi\Services\Middleware;
use Serogaq\TgBotApi\ApiRequest;
use function Serogaq\TgBotApi\Helpers\getBotIdFromToken;

class BotApi {
    /**
     * Create a new class BotApi instance.
     *
     * @param  array  $botConfig  Bot configuration
     */
    public function __construct(protected array $botConfig) {}

    public function __call(string $method, array $arguments = []): mixed {
        /*if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $arguments);
        }*/
        $apiRequest = $this->createRequest($method, $arguments);
        return $apiRequest;
    }

    public function getMe(): ApiRequest {
        return $this->createRequest('getMe', []);
    }

    protected function createRequest(string $method, array $arguments): ApiRequest {
        $middleware = resolve(Middleware::class);
        if(isset($this->botConfig['middleware']) && !empty($this->botConfig['middleware'])) {
            foreach ($this->botConfig['middleware'] as $m) $middleware->addRequestMiddleware($m);
        }
        $apiRequest = $middleware->execRequestMiddlewares(new ApiRequest($method, $arguments, getBotIdFromToken($this->botConfig['token'])));
        return $apiRequest;
    }

}