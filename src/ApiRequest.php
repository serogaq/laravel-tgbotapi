<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Arr;
use Serogaq\TgBotApi\Services\Middleware;
use Serogaq\TgBotApi\Interfaces\HttpClient;

class ApiRequest {

    const FILES = 'files';
    const TIMEOUT = 'request_timeout';
    const CONNECT_TIMEOUT = 'request_connect_timeout';

    protected string $requestId;
    protected HttpClient $httpClient;
    protected int $botId;
    protected BotManager $botManager;
    protected array $botConfig;

    protected array $arguments;
    protected string $method;
    protected array $data = [];
    protected array $files = [];
    /** @var int Timeout of the request in seconds. */
    protected int $timeout = 60;
    /** @var int Connection timeout of the request in seconds. */
    protected int $connectTimeout = 10;
    protected bool $isAsyncRequest = false;

    public function __construct(string $method, array $arguments, int $botId) {
        $this->requestId = mb_substr(md5(random_bytes(10)), 0, 10);
        $this->botId = $botId;
        $this->httpClient = App::make(HttpClient::class);
        $this->botManager = resolve(BotManager::class);
        $this->botConfig = $this->botManager->getBotConfig($botId);
        $this->method = $method;
        $this->arguments = $arguments;
        $this->data = $this->getDataFromArguments($arguments);
        $this->files = $this->getFilesFromArguments($arguments);
        $this->setTimeoutFromArguments($arguments);
        $this->setConnectTimeoutFromArguments($arguments);
    }

    public function __toString(): string {
        return json_encode([
            'requestId' => $this->requestId,
            'botId' => $this->botId,
            'url' => $this->url,
            'method' => $this->method,
            'data' => $this->data,
            'files' => $this->files,
            'timeout' => $this->timeout,
            'connectTimeout' => $this->connectTimeout,
            'isAsyncRequest' => $this->isAsyncRequest,
        ]);
    }

    public function send(): ApiResponse {
        $apiUrl = str_replace('{TOKEN}', $this->botConfig['token'], $this->botConfig['api_url'] ?? config('tgbotapi.api_url'));
        $apiUrl = str_replace('{METHOD}', $this->method, $apiUrl);
        $apiResponse = $this->httpClient
                ->setTimeout($this->timeout)
                ->setConnectTimeout($this->connectTimeout)
                ->send(
                    $apiUrl,
                    empty($this->data) ? 'GET' : 'POST',
                    $this->data,
                    $this->files,
                    $this->isAsyncRequest
                );
        return $this->middlewareResponse($apiResponse);
    }

    protected function getDataFromArguments(array $arguments): array {
        $index = 0;
        // TODO: Exception if arguments[0] (data) is not array
        if(isset($arguments[$index]) && !empty($arguments[$index]) && is_array($arguments[$index])) return $arguments[$index];
        return [];
    }

    protected function getFilesFromArguments(array $arguments): array {
        $index = 1;
        $key = self::FILES;
        // TODO: Exception if arguments[1][FILES] (files) is not array
        if(isset($arguments[$index]) && !empty($arguments[$index]) && isset($arguments[$index][$key]) && is_array($arguments[$index][$key])) return $arguments[$index][$key];
        return [];
    }

    protected function setTimeoutFromArguments(array $arguments): void {
        $index = 1;
        $key = self::TIMEOUT;
        // TODO: Exception if arguments[1][TIMEOUT] (request_timeout) is not integer
        if(isset($arguments[$index]) && !empty($arguments[$index]) && isset($arguments[$index][$key]) && is_int($arguments[$index][$key]))
            $this->timeout = $arguments[$index][$key];
    }

    protected function setConnectTimeoutFromArguments(array $arguments): void {
        $index = 1;
        $key = self::CONNECT_TIMEOUT;
        // TODO: Exception if arguments[1][CONNECT_TIMEOUT] (request_connect_timeout) is not integer
        if(isset($arguments[$index]) && !empty($arguments[$index]) && isset($arguments[$index][$key]) && is_int($arguments[$index][$key]))
            $this->connectTimeout = $arguments[$index][$key];
    }

    public function getRequestId(): string {
        return $this->requestId;
    }

    public function getBotId(): int {
        return $this->botId;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getArguments(): array {
        return $this->arguments;
    }

    public function async(): self {
        $this->isAsyncRequest = true;
        return $this;
    }

    protected function middlewareResponse(ApiResponse $apiResponse): ApiResponse {
        $middleware = resolve(Middleware::class);
        if(isset($this->botConfig['middleware']) && !empty($this->botConfig['middleware'])) {
            foreach ($this->botConfig['middleware'] as $m) $middleware->addResponseMiddleware($m);
        }
        $apiResponse = $middleware->execResponseMiddlewares($apiResponse);
        return $apiResponse;
    }

}