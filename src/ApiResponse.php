<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi;

use Serogaq\TgBotApi\Exceptions\ApiResponseException;
use function Serogaq\TgBotApi\Helpers\arrayToObject;

class ApiResponse implements \Stringable, \ArrayAccess {

    protected string $requestId;
    protected array $response;

    public function __construct(string $requestId, string|array $body) {
        if(is_string($body)) {
            try {
                $this->response = json_decode($body, associative: true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                report($e);
                throw new ApiResponseException('Json parsing error', 0, $e);
            }
        } else
            $this->response = $body;
        if (!is_array($this->response)) throw new ApiResponseException("Incorrect response type:\n".var_export($this->response, true), 1);
        $this->requestId = $requestId;
    }

    public function __toString(): string {
        return json_encode([
            'requestId' => $this->requestId,
            'body' => $this->response
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
        return isset($this->response[$offset]) ? $this->response[$offset] : null;
    }

    public function getRequestId(): string {
        return $this->requestId;
    }
    
    public function asObject(): object {
        return arrayToObject($this->response);
    }

    public function asJson(): array {
        return json_encode($this->response);
    }

}