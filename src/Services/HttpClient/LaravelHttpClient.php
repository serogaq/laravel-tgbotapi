<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Services\HttpClient;

use Illuminate\Http\Client\{ConnectionException, PendingRequest, RequestException};
use Illuminate\Support\Facades\Http;
use Serogaq\TgBotApi\ApiResponse;
use Serogaq\TgBotApi\Exceptions\HttpClientException;
use Serogaq\TgBotApi\Interfaces\HttpClient;

class LaravelHttpClient implements HttpClient {
    protected string $requestId;

    protected PendingRequest $request;

    /** @var int Timeout of the request in seconds. */
    protected int $timeout = 60;

    /** @var int Connection timeout of the request in seconds. */
    protected int $connectTimeout = 10;

    public function __construct() {
        $this->request = Http::withOptions([])->acceptJson()->timeout($this->timeout)->connectTimeout($this->connectTimeout);
    }

    public function setRequestId(string $requestId): self {
        $this->requestId = $requestId;
        return $this;
    }

    public function getRequestId(): string {
        return $this->requestId;
    }

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
    ): ApiResponse {
        if ($method === 'GET') {
            try {
                $response = $this->request
                                ->get($url)
                                ->throw(function ($response, $e) {
                                    report($e);
                                    throw new HttpClientException($e->getMessage(), 1, $e);
                                });
            } catch (ConnectionException | RequestException $e) {
                report($e);
                throw new HttpClientException($e->getMessage(), 2, $e);
            }
        } elseif ($method === 'POST') {
            if (!empty($data) || !empty($files)) {
                $this->request->asMultipart();
            }
            if (!empty($data)) {
                $multipartData = [];
                foreach ($data as $key => $value) {
                    $val = $value;
                    if (is_array($value)) {
                        $val = json_encode($value);
                    }
                    $multipartData[] = ['name' => $key, 'contents' => (string) $val];
                }
            }
            if (!empty($files)) {
                foreach ($files as $key => $path) {
                    $this->request->attach($key, file_get_contents($path), explode('/', $path)[count(explode('/', $path)) - 1]);
                }
            }
            try {
                $response = $this->request
                                ->post($url, $multipartData)
                                ->throw(function ($response, $e) {
                                    report($e);
                                    throw new HttpClientException($e->getMessage(), 1, $e);
                                });
            } catch (ConnectionException | RequestException $e) {
                report($e);
                throw new HttpClientException($e->getMessage(), 2, $e);
            }
        } else {
            throw new HttpClientException("Unsupported Method '{$method}'", 3);
        }
        return new ApiResponse($this->getRequestId(), $response->body());
    }

    /**
     * Get Timeout.
     *
     * @return int
     */
    public function getTimeout(): int {
        return $this->timeout;
    }

    /**
     * Set Timeout.
     *
     * @param  int  $timeout
     * @return $this
     */
    public function setTimeout(int $timeout): self {
        $this->timeout = $timeout;
        $this->request->timeout($timeout);
        return $this;
    }

    /**
     * Get Connection Timeout.
     *
     * @return int
     */
    public function getConnectTimeout(): int {
        return $this->connectTimeout;
    }

    /**
     * Set Connection Timeout.
     *
     * @param  int  $connectTimeout
     * @return $this
     */
    public function setConnectTimeout(int $connectTimeout): self {
        $this->connectTimeout = $connectTimeout;
        $this->request->connectTimeout($connectTimeout);
        return $this;
    }
}
