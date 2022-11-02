<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Illuminate\Support\Facades\App;

trait HandlingEventUpdate {

	public function handleEventUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith('App\Http\Controllers\TgBotApi\EventUpdate', ['event' => $event]);
		$controller->handle();
	}

}