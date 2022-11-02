<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\Updates\StickerUpdate;
use Illuminate\Support\Facades\App;

trait HandlingStickerUpdate {

	public function handleStickerUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(StickerUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}