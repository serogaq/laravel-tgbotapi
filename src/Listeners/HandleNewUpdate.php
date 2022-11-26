<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Listeners;

use Illuminate\Support\Facades\Log;
use Serogaq\TgBotApi\Events\NewUpdateEvent;
use Serogaq\TgBotApi\Facades\BotManager;
use Serogaq\TgBotApi\Traits\ProcessingInControllers;

class HandleNewUpdate {
    use ProcessingInControllers;

    /*public function handle(NewUpdateEvent $event) {
        $logChannel = BotManager::getBotConfig($event->botApi->getBotId())['log_channel'] ?? config('logging.default');
        Log::channel($logChannel)->debug("TgBotApi Listener HandleNewUpdate:\n".(string)$event->update);
    }*/
}
