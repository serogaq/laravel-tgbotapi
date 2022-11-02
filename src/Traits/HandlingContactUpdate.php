<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\TgBotApi\ContactUpdate;
use Illuminate\Support\Facades\App;

trait HandlingContactUpdate {

	public function handleContactUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(ContactUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}