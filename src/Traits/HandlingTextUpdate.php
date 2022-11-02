<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\TgBotApi\TextUpdate;
use Illuminate\Support\Facades\App;

trait HandlingTextUpdate {

	public function handleTextUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(TextUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}