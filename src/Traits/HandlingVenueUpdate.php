<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\Updates\VenueUpdate;
use Illuminate\Support\Facades\App;

trait HandlingVenueUpdate {

	public function handleVenueUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(VenueUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}