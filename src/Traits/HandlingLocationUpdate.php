<?php

namespace Serogaq\TgBotApi\Traits;

use Illuminate\Support\Facades\App;
use Serogaq\TgBotApi\Events\NewUpdateReceived;

trait HandlingLocationUpdate {
    public function handleLocationUpdate(NewUpdateReceived $event) {
        $controller = App::makeWith('App\Http\Controllers\TgBotApi\LocationUpdate', ['event' => $event]);
        $controller->handle();
    }
}
