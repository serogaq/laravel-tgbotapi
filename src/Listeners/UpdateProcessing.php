<?php

namespace Serogaq\TgBotApi\Listeners;

use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Illuminate\Support\Facades\Log;

class UpdateProcessing {
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param  Serogaq\TgBotApi\Events\NewUpdateReceived  $event
	 * @return void
	 */
	public function handle(NewUpdateReceived $event) {
		Log::channel($event->bot->getBotConf()->log_channel)->debug('Listener UpdateProcessing handle NewUpdateReceived event', ['event' => $event]);
	}
}
