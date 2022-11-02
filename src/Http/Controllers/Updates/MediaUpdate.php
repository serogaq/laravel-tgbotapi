<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

use Illuminate\Support\Facades\Log;

class MediaUpdate extends Controller {

	public function handle() {
		Log::channel($this->bot->getBotConf()->log_channel)->debug('MediaUpdate handle', ['update' => $this->update]);
	}

}