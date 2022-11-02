<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\Updates\LocationUpdate;
use Illuminate\Support\Facades\App;

trait HandlingLocationUpdate {

	public function handleLocationUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(LocationUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}