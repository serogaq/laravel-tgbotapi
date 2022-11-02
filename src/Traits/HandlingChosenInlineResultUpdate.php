<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\Updates\ChosenInlineResultUpdate;
use Illuminate\Support\Facades\App;

trait HandlingChosenInlineResultUpdate {

	public function handleChosenInlineResultUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(ChosenInlineResultUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}