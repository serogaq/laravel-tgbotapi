<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Interfaces;

use Serogaq\TgBotApi\{ ApiRequest, ApiResponse };

interface HttpClient {
    
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
     * @return $this
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
     * @return $this
     */
    public function setConnectTimeout(int $connectTimeout): self;

}
