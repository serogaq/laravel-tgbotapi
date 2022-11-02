<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

use Illuminate\Support\Facades\Log;

class CommandUpdate extends Controller {

	public function handle() {
		Log::channel($this->bot->getBotConf()->log_channel)->debug('CommandUpdate handle', ['update' => $this->update]);
	}

}