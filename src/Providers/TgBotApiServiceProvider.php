<?php

namespace Serogaq\TgBotApi\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Console\AboutCommand;
use Serogaq\TgBotApi\Console\Commands\{
    Install,
    MakeUpdateController,
    MakeUpdateListener,
    MakeMiddleware,
    SetWebhook,
    DeleteWebhook,
    GetUpdates
};
use Serogaq\TgBotApi\BotManager;
use Serogaq\TgBotApi\Interfaces\HttpClient;
use Serogaq\TgBotApi\Interfaces\OffsetStore;
use Serogaq\TgBotApi\Providers\EventServiceProvider;
use Serogaq\TgBotApi\Services\Middleware;
use Serogaq\TgBotApi\Services\HttpClient\LaravelHttpClient;
use Serogaq\TgBotApi\Services\OffsetStore\FileOffsetStore;

class TgBotApiServiceProvider extends ServiceProvider {
    /**
     * Bootstrap the application services.
     */
    public function boot() {
        if ($this->app->runningInConsole()) {
            AboutCommand::add('TgBotApi', fn () => [
                'Version' => \Composer\InstalledVersions::getVersion('serogaq/laravel-tgbotapi'),
                'Configured bots' => function () {
                    $count = 0;
                    foreach(config('tgbotapi.bots') as $bot) {
                        if (!empty($bot['username']) && !empty($bot['token'])) $count++;
                    }
                    return $count;
                },
            ]);
            $this->publishes([
                __DIR__ . '/../../config/tgbotapi.php' => config_path('tgbotapi.php'),
            ], 'tgbotapi-config');
            $this->commands([
                Install::class,
                MakeUpdateController::class,
                MakeUpdateListener::class,
                MakeMiddleware::class,
                SetWebhook::class,
                DeleteWebhook::class,
                GetUpdates::class,
            ]);
        }
        $this->registerRoutes();
    }

    /**
     * Register the application services.
     */
    public function register() {
        $this->mergeConfigFrom(__DIR__ . '/../../config/tgbotapi.php', 'tgbotapi');
        $this->app->singleton(BotManager::class, function ($app) {
            return new BotManager(config('tgbotapi.bots'));
        });
        $middleware = new Middleware();
        $this->app->instance(Middleware::class, $middleware);
        $this->app->register(EventServiceProvider::class);
        $this->bindingInterfaces();
    }

    /**
     * Register routes.
     */
    protected function registerRoutes() {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../../routes/tgbotapi.php');
        });
    }

    /**
     * Get route configuration.
     */
    protected function routeConfiguration() {
        return [
            'prefix' => config('tgbotapi.routes.prefix'),
            'middleware' => config('tgbotapi.routes.middleware'),
        ];
    }

    /**
     * Binding Interfaces.
     */
    protected function bindingInterfaces() {
        $this->app->bind(HttpClient::class,
            match (config('tgbotapi.http_client', 'laravel')) {
                'laravel' => LaravelHttpClient::class,
                default => LaravelHttpClient::class,
            }
        );
        $this->app->bind(OffsetStore::class,
            match (config('tgbotapi.offset_store', 'file')) {
                'file' => FileOffsetStore::class,
                default => FileOffsetStore::class,
            }
        );
    }
}
