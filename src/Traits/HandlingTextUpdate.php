<?php

namespace Serogaq\TgBotApi\Traits;

use Illuminate\Support\Facades\App;
use Serogaq\TgBotApi\Events\NewUpdateReceived;

trait HandlingTextUpdate {
    public function handleTextUpdate(NewUpdateReceived $event) {
        $controller = App::makeWith('App\Http\Controllers\TgBotApi\TextUpdate', ['event' => $event]);
        $controller->handle();
    }
}
