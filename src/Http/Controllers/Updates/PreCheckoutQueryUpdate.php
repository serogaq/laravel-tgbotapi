<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

use Illuminate\Support\Facades\Log;

class PreCheckoutQueryUpdate extends Controller {

	public function handle() {
		Log::channel($this->bot->getBotConf()->log_channel)->debug('PreCheckoutQueryUpdate handle', ['update' => $this->update]);
	}

}