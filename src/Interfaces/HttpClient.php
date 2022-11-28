<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Interfaces;

use Serogaq\TgBotApi\Exceptions\HttpClientException;
use Serogaq\TgBotApi\{ ApiRequest, ApiResponse };

interface HttpClient {
    public function setRequestId(string $requestId): self;

    public function getRequestId(): string;

    /**
     * Send HTTP request.
     *
     * @param  string  $url Full URL with query string (if needed)
     * @param  string  $method  HTTP method
     * @param  array  $data
     * @param  array  $files
     * @param  bool|false  $isAsyncRequest
     * @return mixed
     */
    public function send(
        string $url,
        string $method,
        array $data = [],
        array $files = [],
        bool $isAsyncRequest = false
    ): ApiResponse;

    /**
     * Get Timeout.
     *
     * @return int
     */
    public function getTimeout(): int;

    /**
     * Set Timeout.
     *
     * @param  int  $timeout
     * @return self
     */
    public function setTimeout(int $timeout): self;

    /**
     * Get Connection Timeout.
     *
     * @return int
     */
    public function getConnectTimeout(): int;

    /**
     * Set Connection Timeout.
     *
     * @param  int  $connectTimeout
     * @return self
     */
    public function setConnectTimeout(int $connectTimeout): self;

    /**
     * Set Request Hash.
     *
     * @param  string  $requestHash
     * @return self
     */
    public function setRequestHash(string $requestHash): self;

    /**
     * Get Request Hash.
     *
     * @return string
     */
    public function getRequestHash(): string;

    /**
     * To create fake requests.
     *
     * @param  ApiResponse|HttpClientException  $responseOrException
     * @return void
     */
    public function fake(ApiResponse|HttpClientException $responseOrException): void;
}
