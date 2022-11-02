<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\Updates\GameUpdate;
use Illuminate\Support\Facades\App;

trait HandlingGameUpdate {

	public function handleGameUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(GameUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}