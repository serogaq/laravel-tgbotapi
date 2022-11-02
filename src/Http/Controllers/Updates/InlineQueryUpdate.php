<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

use Illuminate\Support\Facades\Log;

class InlineQueryUpdate extends Controller {

	public function handle() {
		Log::channel($this->bot->getBotConf()->log_channel)->debug('InlineQueryUpdate handle', ['update' => $this->update]);
	}

}