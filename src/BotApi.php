<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi;

use Serogaq\TgBotApi\Constants\UpdateChannel;
use Serogaq\TgBotApi\Events\NewUpdateEvent;
use Serogaq\TgBotApi\Exceptions\BotApiException;
use Serogaq\TgBotApi\Interfaces\OffsetStore;
use Serogaq\TgBotApi\Services\Middleware;
use Serogaq\TgBotApi\Updates\Update;
use function Serogaq\TgBotApi\Helpers\{ getBotIdFromToken, isValidBotConfig };

class BotApi {
    /**
     * Create a new class BotApi.
     *
     * @param  array  $botConfig  Bot configuration
     */
    public function __construct(protected array $botConfig) {
        if (!isValidBotConfig($botConfig)) {
            throw new BotApiException('Incorrect bot configuration', 0);
        }
    }

    public function __call(string $method, array $arguments = []): mixed {
        $apiRequest = $this->createApiRequest($method, $arguments);
        return $apiRequest;
    }

    public function getBotId(): int {
        return getBotIdFromToken($this->botConfig['token']);
    }

    public function getUpdatesAndCreateEvents(array $data = [], array $options = []): void {
        $botId = $this->getBotId();
        $offsetStore = resolve(OffsetStore::class);
        if (!isset($data['offset'])) {
            $data['offset'] = $offsetStore->get($botId);
        }
        $arguments[0] = $data;
        $arguments[1] = $options;
        $apiResponse = $this->createApiRequest('getUpdates', $arguments)->send();
        foreach ($apiResponse['result'] as $update) {
            if (isset($update['update_id'])) {
                $offsetStore->set($botId, (int) $update['update_id'] + 1);
            }
            event(new NewUpdateEvent($this, Update::create($update), UpdateChannel::GETUPDATES));
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected function createApiRequest(string $method, array $arguments): ApiRequest {
        $middleware = resolve(Middleware::class);
        return $middleware->applyMiddlewares(
            new ApiRequest($this->getBotId(), $method, $arguments),
            $this->botConfig['middleware'] ?? []
        );
    }
}
