<?php

namespace Serogaq\TgBotApi\Http\Controllers;

use Illuminate\Http\Request;
use Serogaq\TgBotApi\BotManager;
use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Objects\Update;

class WebhookController extends Controller {
    public function webhook(Request $request, $hash) {
        $bot = BotManager::selectBotByHash($hash);
        $data = $request->all();
        $update = new Update($data);
        try {
            event(new NewUpdateReceived($bot, $update, Update::WEBHOOK));
        } catch(\Throwable $e) {
            report($e);
        } finally {
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }
    }
}
