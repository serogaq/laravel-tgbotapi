<?php

namespace Serogaq\TgBotApi\Traits;

use Illuminate\Support\Facades\App;
use Serogaq\TgBotApi\Events\NewUpdateReceived;

trait HandlingPreCheckoutQueryUpdate {
    public function handlePreCheckoutQueryUpdate(NewUpdateReceived $event) {
        $controller = App::makeWith('App\Http\Controllers\TgBotApi\PreCheckoutQueryUpdate', ['event' => $event]);
        $controller->handle();
    }
}
