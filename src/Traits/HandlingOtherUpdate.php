<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\TgBotApi\OtherUpdate;
use Illuminate\Support\Facades\App;

trait HandlingOtherUpdate {

	public function handleOtherUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(OtherUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}