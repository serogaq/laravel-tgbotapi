<?php

namespace Serogaq\TgBotApi\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Serogaq\TgBotApi\Events\NewUpdateReceived;
use Serogaq\TgBotApi\Listeners\UpdateProcessing;

class EventServiceProvider extends ServiceProvider {
	
	protected $listen = [
		NewUpdateReceived::class => [
			UpdateProcessing::class,
		]
	];

	/**
	 * Register any events for your application.
	 *
	 * @return void
	 */
	public function boot() {
		parent::boot();
	}
}