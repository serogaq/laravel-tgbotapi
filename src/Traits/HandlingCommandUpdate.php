<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\TgBotApi\CommandUpdate;
use Illuminate\Support\Facades\App;

trait HandlingCommandUpdate {

	public function handleCommandUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(CommandUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}