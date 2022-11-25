<?php

use Illuminate\Support\Facades\Route;
use Serogaq\TgBotApi\Http\Controllers\WebhookController;

Route::post('/webhook/{token}', [WebhookController::class, 'webhook'])->where('token', '[0-9]+:[a-zA-Z0-9_-]+')->name('tgbotapi.webhook');
