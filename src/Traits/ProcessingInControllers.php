<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Serogaq\TgBotApi\Events\NewUpdateEvent;
use Serogaq\TgBotApi\Facades\BotManager;

trait ProcessingInControllers {
    /**
     * Handle the event.
     *
     * @param  NewUpdateEvent  $event
     * @return void
     */
    public function handle(NewUpdateEvent $event) {
        $logChannel = BotManager::getBotConfig($event->botApi->getBotId())['log_channel'] ?? config('logging.default');
        Log::channel($logChannel)->debug("TgBotApi Listener HandleNewUpdate:\n" . (string) $event->update);
        $updateClassName = (new \ReflectionClass(get_class($event->update)))->getShortName();
        $updateControllerClassName = "App\\TgBotApi\\Updates\\{$updateClassName}";
        if (!class_exists($updateControllerClassName)) {
            return;
        }
        $updateController = App::make($updateControllerClassName, ['event' => $event]);
        $updateController->handle();
    }
}
