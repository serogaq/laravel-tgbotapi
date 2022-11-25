<?php
declare(strict_types=1);

namespace Serogaq\TgBotApi\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Serogaq\TgBotApi\Events\NewUpdateEvent;
use Serogaq\TgBotApi\Listeners\HandleNewUpdate;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NewUpdateEvent::class => [
            HandleNewUpdate::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
