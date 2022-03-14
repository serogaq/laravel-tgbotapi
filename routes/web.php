<?php

use Illuminate\Support\Facades\Route;
use Serogaq\TgBotApi\Http\Controllers\WebhookController;

Route::post('/tgbotapi/webhook/{hash}', [WebhookController::class, 'webhook'])->name('tgbotapi.webhook');