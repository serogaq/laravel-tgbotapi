<?php

namespace Serogaq\TgBotApi;

use Illuminate\Support\ServiceProvider;
// use Illuminate\Console\Scheduling\Schedule;
use Serogaq\TgBotApi\Console\{DeleteWebhook, GetUpdates, InstallTgBotApi, MakeTgBotApiController, MakeUpdateProcessingListener, SetWebhook};
use Serogaq\TgBotApi\Providers\EventServiceProvider;

class TgBotApiServiceProvider extends ServiceProvider {
    /**
     * Bootstrap the application services.
     */
    public function boot() {
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/tgbotapi.php' => config_path('tgbotapi.php'),
            ], 'tgbotapi-config');

            $this->commands([
                InstallTgBotApi::class,
                MakeUpdateProcessingListener::class,
                MakeTgBotApiController::class,
                SetWebhook::class,
                DeleteWebhook::class,
                GetUpdates::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register() {
        $this->mergeConfigFrom(__DIR__ . '/../config/tgbotapi.php', 'tgbotapi');

        $this->app->singleton(BotManager::class, function ($app) {
            return new BotManager;
        });

        /*$this->app->bind(BotManager::class, function ($app) {
            return new BotManager;
        });*/

        // $this->app->register(EventServiceProvider::class);
    }
}
