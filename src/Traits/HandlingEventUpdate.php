<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\Updates\EventUpdate;
use Illuminate\Support\Facades\App;

trait HandlingEventUpdate {

	public function handleEventUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(EventUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}