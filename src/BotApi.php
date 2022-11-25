<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi;

use Serogaq\TgBotApi\Exceptions\ApiClientException;
use Serogaq\TgBotApi\Interfaces\OffsetStore;
use Serogaq\TgBotApi\Services\Middleware;
use Serogaq\TgBotApi\ApiRequest;
use Serogaq\TgBotApi\Updates\Update;
use Serogaq\TgBotApi\Events\NewUpdateEvent;
use Serogaq\TgBotApi\Constants\UpdateChannel;
use function Serogaq\TgBotApi\Helpers\getBotIdFromToken;

class BotApi {
    /**
     * Create a new class BotApi.
     *
     * @param  array  $botConfig  Bot configuration
     */
    public function __construct(protected array $botConfig) {}

    public function __call(string $method, array $arguments = []): mixed {
        /*if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $arguments);
        }*/
        $apiRequest = $this->createRequest($method, $arguments);
        return $apiRequest;
    }

    public function getBotId(): int {
        return getBotIdFromToken($this->botConfig['token']);
    }

    public function getUpdatesAndCreateEvents(array $data = [], array $options = []): void {
        $botId = $this->getBotId();
        $offsetStore = resolve(OffsetStore::class);
        if (!isset($data['offset'])) $data['offset'] = $offsetStore->get($botId);
        $arguments[0] = $data;
        $arguments[1] = $options;
        $apiResponse = $this->createRequest('getUpdates', $arguments)->send();
        foreach ($apiResponse['result'] as $update) {
            if (isset($update['update_id'])) $offsetStore->set($botId, (int) $update['update_id'] + 1);
            event(new NewUpdateEvent($this, Update::create($update), UpdateChannel::GETUPDATES));
        }
    }

    protected function createRequest(string $method, array $arguments): ApiRequest {
        $middleware = resolve(Middleware::class);
        if(isset($this->botConfig['middleware']) && !empty($this->botConfig['middleware'])) {
            foreach ($this->botConfig['middleware'] as $m) $middleware->addRequestMiddleware($m);
        }
        $apiRequest = $middleware->execRequestMiddlewares(new ApiRequest($method, $arguments, $this->getBotId()));
        return $apiRequest;
    }

}