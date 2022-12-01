<?php

namespace Serogaq\TgBotApi\Exceptions;

use Serogaq\TgBotApi\ApiResponse;

class HttpClientException extends TgBotApiException {
    protected ?ApiResponse $apiResponse;

    public function __construct(string $message = 'Unknown error', int $code = 0, ?ApiResponse $apiResponse = null, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->apiResponse = $apiResponse;
    }

    public function getApiResponse(): ?ApiResponse {
        return $this->apiResponse;
    }
}
