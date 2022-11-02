<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Illuminate\Support\Facades\App;

trait HandlingPreCheckoutQueryUpdate {

	public function handlePreCheckoutQueryUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith('App\Http\Controllers\TgBotApi\PreCheckoutQueryUpdate', ['event' => $event]);
		$controller->handle();
	}

}