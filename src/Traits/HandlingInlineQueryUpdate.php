<?php

namespace Serogaq\TgBotApi\Traits;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Illuminate\Support\Facades\App;

trait HandlingInlineQueryUpdate {

	public function handleInlineQueryUpdate(NewUpdateReceived $event) {
		$controller = App::makeWith('App\Http\Controllers\TgBotApi\InlineQueryUpdate', ['event' => $event]);
		$controller->handle();
	}

}