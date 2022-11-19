<?php

namespace Serogaq\TgBotApi\Traits;

use Illuminate\Support\Facades\App;
use Serogaq\TgBotApi\Events\NewUpdateReceived;

trait HandlingChosenInlineResultUpdate {
    public function handleChosenInlineResultUpdate(NewUpdateReceived $event) {
        $controller = App::makeWith('App\Http\Controllers\TgBotApi\ChosenInlineResultUpdate', ['event' => $event]);
        $controller->handle();
    }
}
