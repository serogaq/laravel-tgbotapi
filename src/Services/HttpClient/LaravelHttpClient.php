<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Services\HttpClient;

use Illuminate\Http\Client\{ConnectionException, PendingRequest, RequestException};
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Serogaq\TgBotApi\ApiResponse;
use Serogaq\TgBotApi\Exceptions\HttpClientException;
use Serogaq\TgBotApi\Interfaces\HttpClient;

class LaravelHttpClient implements HttpClient {
    protected string $requestHash;

    protected string $requestId;

    protected PendingRequest $request;

    /** @var int Timeout of the request in seconds. */
    protected int $timeout = 60;

    /** @var int Connection timeout of the request in seconds. */
    protected int $connectTimeout = 10;

    public function __construct() {
        // TODO: ConnectTimeout
        $this->request = Http::withOptions([])->acceptJson()->timeout($this->timeout);
    }

    public function setRequestHash(string $requestHash): self {
        $this->requestHash = $requestHash;
        return $this;
    }

    public function getRequestHash(): string {
        return $this->requestHash;
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
        $cacheFakeRequestKey = 'tgbotapi_httpclientfake_' . $this->getRequestHash();
        if (Cache::has($cacheFakeRequestKey)) {
            $responseOrException = json_decode(Cache::pull($cacheFakeRequestKey), true);
            if ($responseOrException['type'] === 'ApiResponse') {
                return new ApiResponse($responseOrException['body'], $responseOrException['statusCode'], $responseOrException['requestId']);
            } elseif ($responseOrException['type'] === 'HttpClientException') {
                if (isset($responseOrException['ApiResponseBody'])) {
                    throw new HttpClientException(
                        $responseOrException['message'],
                        $responseOrException['code'],
                        new ApiResponse($responseOrException['ApiResponseBody'], $responseOrException['ApiResponseStatusCode'] ?? 200, $responseOrException['requestId'])
                    );
                } else {
                    throw new HttpClientException($responseOrException['message'], $responseOrException['code']);
                } // @codeCoverageIgnore
            }
        }
        if ($method === 'GET') {
            try {
                $response = $this->request
                                ->get($url)
                                ->throw(function ($response, $e) {
                                    report($e); // @codeCoverageIgnore
                                    if ($e instanceof ConnectionException) {
                                        throw new HttpClientException($e->getMessage(), 1, null, $e);
                                    }
                                    if ($e instanceof RequestException) {
                                        throw new HttpClientException($e->getMessage(), 2, new ApiResponse($response->body(), $response->status(), $this->getRequestId()), $e);
                                    }
                                });
            } catch (ConnectionException $e) {
                report($e); // @codeCoverageIgnore
                throw new HttpClientException($e->getMessage(), 1, null, $e);
            } catch (RequestException $e) {
                report($e); // @codeCoverageIgnore
                throw new HttpClientException($e->getMessage(), 2, null, $e);
            }
        } elseif ($method === 'POST') {
            if (!empty($data) || !empty($files)) {
                $this->request->asMultipart();
            }
            $multipartData = [];
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $val = $value;
                    if (is_array($value)) {
                        $val = json_encode($value);
                    }
                    $multipartData[] = ['name' => $key, 'contents' => (string) $val];
                }
            }
            if (!empty($files)) {
                foreach ($files as $file) {
                    $key = $file[0] ?? null;
                    $rawContentOrStream = $file[1] ?? null;
                    $filename = $file[2] ?? $key;
                    if (is_null($key) || is_null($rawContentOrStream)) {
                        continue;
                    }
                    $this->request->attach($key, $rawContentOrStream, $filename);
                }
            }
            try {
                $response = $this->request
                                ->post($url, $multipartData)
                                ->throw(function ($response, $e) {
                                    report($e); // @codeCoverageIgnore
                                    if ($e instanceof ConnectionException) {
                                        throw new HttpClientException($e->getMessage(), 1, null, $e);
                                    }
                                    if ($e instanceof RequestException) {
                                        throw new HttpClientException($e->getMessage(), 2, new ApiResponse($response->body(), $response->status(), $this->getRequestId()), $e);
                                    }
                                });
            } catch (ConnectionException $e) {
                report($e); // @codeCoverageIgnore
                throw new HttpClientException($e->getMessage(), 1, null, $e);
            } catch (RequestException $e) {
                report($e); // @codeCoverageIgnore
                throw new HttpClientException($e->getMessage(), 2, null, $e);
            }
        } else {
            throw new HttpClientException("Unsupported Method '{$method}'", 3); // @codeCoverageIgnore
        }
        return new ApiResponse($response->body(), $response->getStatusCode(), $this->getRequestId());
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
        // $this->request->connectTimeout($connectTimeout);
        return $this;
    }

    /**
     * To create fake requests.
     *
     * @param  ApiResponse|HttpClientException  $responseOrException
     * @return void
     */
    public function fake(ApiResponse|HttpClientException $responseOrException): void {
        Cache::put('tgbotapi_httpclientfake_' . $this->getRequestHash(), match (get_class($responseOrException)) {
            ApiResponse::class => json_encode(['type' => 'ApiResponse', 'body' => $responseOrException->asJson(), 'statusCode' => $responseOrException->getStatusCode(), 'requestId' => $this->getRequestId()]),
            HttpClientException::class => json_encode(['type' => 'HttpClientException', 'message' => $responseOrException->getMessage(), 'code' => $responseOrException->getCode(), 'ApiResponseBody' => $responseOrException->getApiResponse()?->asJson(), 'ApiResponseStatusCode' => $responseOrException->getApiResponse()?->getStatusCode(), 'requestId' => $this->getRequestId()])
        }, 60);
    }
}
