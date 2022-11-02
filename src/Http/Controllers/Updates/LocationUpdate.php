<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

use Illuminate\Support\Facades\Log;

class LocationUpdate extends Controller {

	public function handle() {
		Log::channel($this->bot->getBotConf()->log_channel)->debug('LocationUpdate handle', ['update' => $this->update]);
	}

}