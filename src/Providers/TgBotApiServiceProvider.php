<?php

namespace Serogaq\TgBotApi\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;
//use Serogaq\TgBotApi\Console\{DeleteWebhook, GetUpdates, InstallTgBotApi, MakeTgBotApiController, MakeUpdateProcessingListener, SetWebhook};
use Serogaq\TgBotApi\Console\InstallTgBotApi;
use Serogaq\TgBotApi\BotManager;
use Serogaq\TgBotApi\Interfaces\HttpClient;
use Serogaq\TgBotApi\Services\Middleware;
use Serogaq\TgBotApi\Services\HttpClient\LaravelHttpClient;

class TgBotApiServiceProvider extends ServiceProvider {
    /**
     * Bootstrap the application services.
     */
    public function boot() {
        AboutCommand::add('TgBotApi', fn () => ['Version' => '2.0.0-alpha']);
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/tgbotapi.php' => config_path('tgbotapi.php'),
            ], 'tgbotapi-config');
            $this->commands([
                InstallTgBotApi::class,
                //MakeUpdateProcessingListener::class,
                //MakeTgBotApiController::class,
                //SetWebhook::class,
                //DeleteWebhook::class,
                //GetUpdates::class,
            ]);
        }
        $this->registerRoutes();
    }

    /**
     * Register the application services.
     */
    public function register() {
        $this->mergeConfigFrom(__DIR__ . '/../config/tgbotapi.php', 'tgbotapi');
        $this->app->singleton(BotManager::class, function ($app) {
            return new BotManager(config('tgbotapi'));
        });
        $middleware = new Middleware();
        $this->app->instance(Middleware::class, $middleware);
        $this->bindingInterfaces();
    }

    /**
     * Register routes.
     */
    protected function registerRoutes() {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
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
    }
}
