<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

use Illuminate\Support\Facades\Log;

class GameUpdate extends Controller {

	public function handle() {
		Log::channel($this->bot->getBotConf()->log_channel)->debug('GameUpdate handle', ['update' => $this->update]);
	}

}