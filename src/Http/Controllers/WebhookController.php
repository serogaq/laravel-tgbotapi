<?php

namespace Serogaq\TgBotApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Serogaq\TgBotApi\BotManager;
use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Objects\Update;

class WebhookController extends Controller {

	public function webhook(Request $request, $hash) {
		$bot = BotManager::selectBotByHash($hash);
		$data = $request->all();
		$update = new Update($data);
		event(new NewUpdateReceived($bot, $update, Update::WEBHOOK));
	}

}
    