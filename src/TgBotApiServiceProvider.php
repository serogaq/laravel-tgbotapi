<?php

namespace Serogaq\TgBotApi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
//use Illuminate\Console\Scheduling\Schedule;
use Serogaq\TgBotApi\Providers\EventServiceProvider;
use Serogaq\TgBotApi\Console\{InstallTgBotApi, MakeUpdateProcessingListener, SetWebhook, DeleteWebhook, GetUpdates};

class TgBotApiServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap the application services.
	 */
	public function boot() {
		/*$this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
			$schedule->command('some:command')->everyMinute();
        });*/

		//$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
		$this->loadRoutesFrom(__DIR__.'/../routes/web.php');

		if($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__.'/../config/tgbotapi.php' => config_path('tgbotapi.php'),
			], 'tgbotapi-config');

			$this->commands([
				InstallTgBotApi::class,
				MakeUpdateProcessingListener::class,
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
		$this->mergeConfigFrom(__DIR__.'/../config/tgbotapi.php', 'tgbotapi');

		$this->app->singleton(BotManager::class, function ($app) {
			return new BotManager;
		});

		/*$this->app->bind(BotManager::class, function ($app) {
			return new BotManager;
		});*/

		//$this->app->register(EventServiceProvider::class);
	}
}
