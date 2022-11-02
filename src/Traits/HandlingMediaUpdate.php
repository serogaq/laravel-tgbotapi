<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\Updates\MediaUpdate;
use Illuminate\Support\Facades\App;

trait HandlingMediaUpdate {

	public function handleMediaUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(MediaUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}