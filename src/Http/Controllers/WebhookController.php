<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Serogaq\TgBotApi\Facades\BotManager;
use Serogaq\TgBotApi\Updates\Update;
use Serogaq\TgBotApi\Events\NewUpdateEvent;
use Serogaq\TgBotApi\Constants\UpdateChannel;
use Serogaq\TgBotApi\Interfaces\OffsetStore;
use function Serogaq\TgBotApi\Helpers\getBotIdFromToken;

class WebhookController extends Controller {
    public function webhook(Request $request, string $token): ?JsonResponse {
        $botId = getBotIdFromToken($token);
        if (is_null($botId)) return null;
        $logChannel = BotManager::getBotConfig($botId)['log_channel'] ?? config('logging.default');
        $botApi = BotManager::bot($botId);
        if (is_null($botApi)) {
            Log::channel($logChannel)->debug("TgBotApi Webhook - error creating botApi instance; Raw request:\n".(string)$request);
            return null;
        }
        $offsetStore = resolve(OffsetStore::class);
        $update = $request->all();
        if(empty($update)) {
            Log::channel($logChannel)->debug("TgBotApi Webhook - empty update array; Raw request:\n".(string)$request);
            return null;
        }
        if (isset($update['update_id'])) $offsetStore->set($botId, (int) $update['update_id'] + 1);
        try {
            event(new NewUpdateEvent($botApi, Update::create($update), UpdateChannel::WEBHOOK));
        } catch(\Throwable $e) {
            report($e);
        } finally {
            return response()->json(['ok' => true], 200);
        }
    }
}
