<?php

use Illuminate\Support\Facades\Route;
use Serogaq\TgBotApi\Http\Controllers\WebhookController;

Route::post('/webhook/{hash}', [WebhookController::class, 'webhook'])->name('tgbotapi.webhook');
