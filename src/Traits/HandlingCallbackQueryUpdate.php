<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Illuminate\Support\Facades\App;

trait HandlingCallbackQueryUpdate {

	public function handleCallbackQueryUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith('App\Http\Controllers\TgBotApi\CallbackQueryUpdate', ['event' => $event]);
		$controller->handle();
	}

}