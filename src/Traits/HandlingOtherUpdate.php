<?php

namespace Serogaq\TgBotApi\Traits;

use Illuminate\Support\Facades\App;
use Serogaq\TgBotApi\Events\NewUpdateReceived;

trait HandlingOtherUpdate {
    public function handleOtherUpdate(NewUpdateReceived $event) {
        $controller = App::makeWith('App\Http\Controllers\TgBotApi\OtherUpdate', ['event' => $event]);
        $controller->handle();
    }
}
