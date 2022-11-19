<?php

namespace Serogaq\TgBotApi\Traits;

use Illuminate\Support\Facades\App;
use Serogaq\TgBotApi\Events\NewUpdateReceived;

trait HandlingVenueUpdate {
    public function handleVenueUpdate(NewUpdateReceived $event) {
        $controller = App::makeWith('App\Http\Controllers\TgBotApi\VenueUpdate', ['event' => $event]);
        $controller->handle();
    }
}
