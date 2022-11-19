<?php

namespace Serogaq\TgBotApi\Traits;

use Illuminate\Support\Facades\App;
use Serogaq\TgBotApi\Events\NewUpdateReceived;

trait HandlingCallbackQueryUpdate {
    public function handleCallbackQueryUpdate(NewUpdateReceived $event) {
        $controller = App::makeWith('App\Http\Controllers\TgBotApi\CallbackQueryUpdate', ['event' => $event]);
        $controller->handle();
    }
}
