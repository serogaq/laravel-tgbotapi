<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi;

use Illuminate\Support\Facades\Log;
use Serogaq\TgBotApi\Exceptions\{ ApiRequestException, HttpClientException };
use Serogaq\TgBotApi\Interfaces\HttpClient;
use Serogaq\TgBotApi\Services\Middleware;

class ApiRequest implements \Stringable {
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

    protected string $url;

    protected array $data = [];

    protected array $files = [];

    /** @var int Timeout of the request in seconds. */
    protected int $timeout = 60;

    /** @var int Connection timeout of the request in seconds. */
    protected int $connectTimeout = 10;

    protected bool $isAsyncRequest = false;

    public function __construct(int $botId, string $method, array $arguments = []) {
        $this->requestId = mb_substr(md5(random_bytes(10)), 0, 10);
        $this->botId = $botId;
        $this->httpClient = resolve(HttpClient::class);
        $this->botManager = resolve(BotManager::class);
        if (!$this->botManager->botExists($botId)) {
            throw new ApiRequestException('Incorrect bot configuration', 0);
        }
        $this->botConfig = $this->botManager->getBotConfig($botId);
        $this->method = $method;
        $this->arguments = $arguments;
        $apiUrl = str_replace('{TOKEN}', $this->botConfig['token'], $this->botConfig['api_url'] ?? config('tgbotapi.api_url', 'https://api.telegram.org/bot{TOKEN}/{METHOD}'));
        $apiUrl = str_replace('{METHOD}', $this->method, $apiUrl);
        $this->url = $apiUrl;
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
        Log::channel($this->botConfig['log_channel'] ?? config('logging.default'))->debug("TgBotApi ApiRequest send:\n" . (string) $this);
        $apiResponse = $this->httpClient
                ->setRequestId($this->requestId)
                ->setRequestHash($this->getRequestHash())
                ->setTimeout($this->timeout)
                ->setConnectTimeout($this->connectTimeout)
                ->send(
                    $this->url,
                    empty($this->data) ? 'GET' : 'POST',
                    $this->data,
                    $this->files,
                    $this->isAsyncRequest
                );
        return $this->applyMiddlewares($apiResponse);
    }

    public function withFakeResponse(ApiResponse|HttpClientException $responseOrException): self {
        $this->httpClient
            ->setRequestId($this->requestId)
            ->setRequestHash($this->getRequestHash())
            ->setTimeout($this->timeout)
            ->setConnectTimeout($this->connectTimeout)
            ->fake($responseOrException);
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getDataFromArguments(array $arguments): array {
        $index = 0;
        // TODO: Exception if arguments[0] (data) is not array
        if (isset($arguments[$index]) && !empty($arguments[$index]) && is_array($arguments[$index])) {
            return $arguments[$index];
        }
        return [];
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getFilesFromArguments(array $arguments): array {
        $index = 1;
        $key = self::FILES;
        // TODO: Exception if arguments[1][FILES] (files) is not array
        if (isset($arguments[$index]) && !empty($arguments[$index]) && isset($arguments[$index][$key]) && is_array($arguments[$index][$key])) {
            return $arguments[$index][$key];
        }
        return [];
    }

    /**
     * @codeCoverageIgnore
     */
    protected function setTimeoutFromArguments(array $arguments): void {
        $index = 1;
        $key = self::TIMEOUT;
        // TODO: Exception if arguments[1][TIMEOUT] (request_timeout) is not integer
        if (isset($arguments[$index]) && !empty($arguments[$index]) && isset($arguments[$index][$key]) && is_int($arguments[$index][$key])) {
            $this->timeout = $arguments[$index][$key];
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected function setConnectTimeoutFromArguments(array $arguments): void {
        $index = 1;
        $key = self::CONNECT_TIMEOUT;
        // TODO: Exception if arguments[1][CONNECT_TIMEOUT] (request_connect_timeout) is not integer
        if (isset($arguments[$index]) && !empty($arguments[$index]) && isset($arguments[$index][$key]) && is_int($arguments[$index][$key])) {
            $this->connectTimeout = $arguments[$index][$key];
        }
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

    /**
     * @codeCoverageIgnore
     */
    public function async(): self {
        $this->isAsyncRequest = true;
        return $this;
    }

    public function getRequestHash(): string {
        return md5(json_encode([
            'botId' => $this->botId,
            'url' => $this->url,
            'method' => $this->method,
            'data' => $this->data,
            'files' => $this->files,
            'timeout' => $this->timeout,
            'connectTimeout' => $this->connectTimeout,
            'isAsyncRequest' => $this->isAsyncRequest,
        ]));
    }

    /**
     * @codeCoverageIgnore
     */
    protected function applyMiddlewares(ApiResponse $apiResponse): ApiResponse {
        $middleware = resolve(Middleware::class);
        $response = $middleware->applyMiddlewares(
            $apiResponse,
            $this->botConfig['middleware'] ?? []
        );
        Log::channel($this->botConfig['log_channel'] ?? config('logging.default'))->debug("TgBotApi ApiResponse:\n" . (string) $response);
        return $response;
    }
}
