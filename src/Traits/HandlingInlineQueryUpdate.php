<?php

namespace Serogaq\TgBotApi\Traits;

use Illuminate\Support\Facades\App;
use Serogaq\TgBotApi\Events\NewUpdateReceived;

trait HandlingInlineQueryUpdate {
    public function handleInlineQueryUpdate(NewUpdateReceived $event) {
        $controller = App::makeWith('App\Http\Controllers\TgBotApi\InlineQueryUpdate', ['event' => $event]);
        $controller->handle();
    }
}
