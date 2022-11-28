<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi;

use Serogaq\TgBotApi\Exceptions\ApiResponseException;
use function Serogaq\TgBotApi\Helpers\arrayToObject;

class ApiResponse implements \Stringable, \ArrayAccess {
    protected ?string $requestId;

    protected array $response;

    public function __construct(string|array $body, ?string $requestId = null) {
        if (is_string($body)) {
            try {
                $response = json_decode($body, associative: true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                report($e);
                throw new ApiResponseException('Json parsing error', 0, $e);
            }
        } elseif (is_array($body) && !empty($body)) {
            $response = $body;
        }
        if (!isset($response) || !is_array($response)) {
            throw new ApiResponseException("Invalid body:\n" . var_export($body, true), 1);
        }
        $this->response = $response;
        $this->requestId = $requestId;
    }

    public function __toString(): string {
        return json_encode([
            'requestId' => $this->requestId,
            'body' => $this->response,
        ]);
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        // Cannot be modified
    }

    public function offsetExists(mixed $offset): bool {
        return isset($this->response[$offset]);
    }

    public function offsetUnset(mixed $offset): void {
        // Cannot be deleted
    }

    public function offsetGet(mixed $offset): mixed {
        return $this->response[$offset] ?? null;
    }

    public function getRequestId(): ?string {
        return $this->requestId;
    }

    public function asArray(): array {
        return $this->response;
    }

    public function asObject(): object {
        return arrayToObject($this->response);
    }

    public function asJson(): string {
        return json_encode($this->response);
    }
}
