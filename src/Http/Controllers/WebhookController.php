<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Serogaq\TgBotApi\Facades\BotManager;
use Serogaq\TgBotApi\Updates\Update;
use Serogaq\TgBotApi\Events\NewUpdateEvent;
use Serogaq\TgBotApi\Constants\UpdateChannel;
use Serogaq\TgBotApi\Interfaces\OffsetStore;
use function Serogaq\TgBotApi\Helpers\getBotIdFromToken;

class WebhookController extends Controller {
    public function webhook(Request $request, string $token): ?Response {
        $botId = getBotIdFromToken($token);
        if (is_null($botId)) return null;
        $botApi = BotManager::bot($botId);
        if (is_null($botApi)) return null;
        $offsetStore = resolve(OffsetStore::class);
        $update = $request->all();
        if (isset($update['update_id'])) $offsetStore->set($botId, (int) $update['update_id'] + 1);
        try {
            event(new NewUpdateEvent($botApi, Update::create($update), UpdateChannel::WEBHOOK));
        } catch(\Throwable $e) {
            report($e);
        } finally {
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }
    }
}
