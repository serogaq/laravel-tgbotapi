<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Illuminate\Support\Facades\App;

trait HandlingChosenInlineResultUpdate {

	public function handleChosenInlineResultUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith('App\Http\Controllers\TgBotApi\ChosenInlineResultUpdate', ['event' => $event]);
		$controller->handle();
	}

}