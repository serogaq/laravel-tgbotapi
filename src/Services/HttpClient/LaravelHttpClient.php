<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Services\HttpClient;

use Serogaq\TgBotApi\Interfaces\HttpClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\{ ConnectionException, RequestException };
use Serogaq\TgBotApi\ApiResponse;
use Serogaq\TgBotApi\Services\Middleware;
use Serogaq\TgBotApi\Exceptions\HttpClientException;

class LaravelHttpClient implements HttpClient {
    
    protected Http $client;

    /** @var int Timeout of the request in seconds. */
    protected int $timeout = 60;

    /** @var int Connection timeout of the request in seconds. */
    protected int $connectTimeout = 10;

    public function __construct() {
        $this->client = Http::withOptions([])->acceptJson()->timeout($this->timeout)->connectTimeout($this->connectTimeout);
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
                $response = $this->client
                                ->get($url)
                                ->throw(function ($response, $e) {
                                    report($e);
                                    throw new HttpClientException($e->getMessage(), 1, $e);
                                });
            } catch (ConnectionException|RequestException $e) {
                report($e);
                throw new HttpClientException($e->getMessage(), 2, $e);
            }
        } elseif ($method === 'POST') {
            if (!empty($data) || !empty($files)) $this->client->asMultipart();
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
                    $this->client->attach($key, file_get_contents($path), explode('/', $path)[count(explode('/', $path)) - 1]);
                }
            }
            try {
                $response = $this->client
                                ->post($url, $multipartData)
                                ->throw(function ($response, $e) {
                                    report($e);
                                    throw new HttpClientException($e->getMessage(), 1, $e);
                                });
            } catch (ConnectionException|RequestException $e) {
                report($e);
                throw new HttpClientException($e->getMessage(), 2, $e);
            }
        }
        else throw new HttpClientException("Unsupported Method '{$method}'", 3);
        return new ApiResponse($response->body());
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
        $this->client->timeout($timeout);
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
        $this->client->connectTimeout($connectTimeout);
        return $this;
    }

}
