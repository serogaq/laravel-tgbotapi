<?php
declare(strict_types=1);

namespace App\TgBotApi;

use Serogaq\TgBotApi\Exceptions\ApiResponseException;

class ApiResponse {

    protected array $response;

    public function __construct(string $body) {
        try {
            $this->response = json_decode($body, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            report($e);
            throw new ApiResponseException('Json parsing error', 0, $e);
        }
        if (!is_array($this->response)) throw new ApiResponseException("Incorrect response type:\n".var_export($this->response, true), 1);
    }

    public function asArray(): array {
        return $this->response;
    }

    public function asObject(): object {
        return (object) $this->response;
    }

}