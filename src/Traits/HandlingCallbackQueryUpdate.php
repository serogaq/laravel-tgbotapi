<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\Updates\CallbackQueryUpdate;
use Illuminate\Support\Facades\App;

trait HandlingCallbackQueryUpdate {

	public function handleCallbackQueryUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(CallbackQueryUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}