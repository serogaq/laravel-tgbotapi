<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\TgBotApi\PreCheckoutQueryUpdate;
use Illuminate\Support\Facades\App;

trait HandlingPreCheckoutQueryUpdate {

	public function handlePreCheckoutQueryUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(PreCheckoutQueryUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}