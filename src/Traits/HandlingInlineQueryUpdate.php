<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Http\Controllers\TgBotApi\InlineQueryUpdate;
use Illuminate\Support\Facades\App;

trait HandlingInlineQueryUpdate {

	public function handleInlineQueryUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith(InlineQueryUpdate::class, ['event' => $event]);
		$controller->handle();
	}

}