<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Illuminate\Support\Facades\App;

trait HandlingTextUpdate {

	public function handleTextUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith('App\Http\Controllers\TgBotApi\TextUpdate', ['event' => $event]);
		$controller->handle();
	}

}