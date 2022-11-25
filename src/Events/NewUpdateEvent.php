<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Serogaq\TgBotApi\BotApi;
use Serogaq\TgBotApi\Interfaces\Update;

class NewUpdateEvent {
    use Dispatchable, SerializesModels;

    public BotApi $botApi;

    public Update $update;

    public int $updateChannel;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BotApi $botApi, Update $update, int $updateChannel) {
        $this->botApi = $botApi;
        $this->update = $update;
        $this->updateChannel = $updateChannel;
    }
}
