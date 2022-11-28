# Laravel TgBotApi

[![Latest Stable Version](http://poser.pugx.org/serogaq/laravel-tgbotapi/v)](https://packagist.org/packages/serogaq/laravel-tgbotapi)
[![License](http://poser.pugx.org/serogaq/laravel-tgbotapi/license)](https://packagist.org/packages/serogaq/laravel-tgbotapi)

This package provides methods for working with the telegram bot api, and helpers for receiving updates via webhooks or the polling method  
Requires `PHP >= 8.0.2` and `Laravel >= 8`

## Installation

Require the `serogaq/laravel-tgbotapi` package in your `composer.json` and update your dependencies:

```bash
composer require serogaq/laravel-tgbotapi
```

Then run the command:

```bash
php artisan tgbotapi:install
```

## Usage

Set up your bot in `config/tgbotapi.php`  

Add a listener for the new update event:

```php
// app/Providers/EventServiceProvider.php
use App\Listeners\HandleNewUpdate;
use Serogaq\TgBotApi\Events\NewUpdateEvent;
class EventServiceProvider extends ServiceProvider {
	protected $listen = [
		NewUpdateEvent::class => [
			HandleNewUpdate::class,
		],
	];
}
```

To receive updates via webhooks, use the command:

```bash
php artisan tgbotapi:setwebhook
```

To receive updates via a long polling, create a background task:

```php
// app/Console/Kernel.php
class Kernel extends ConsoleKernel {
	protected function schedule(Schedule $schedule) {
		$schedule->command('tgbotapi:getupdates', ['bot_username', '--until-complete'])->everyMinute()->withoutOverlapping()->runInBackground();
	}
}
```

Events will be sent to `app/Listeners/HandleNewUpdate.php` where you can process them.

Package provides the logic for handling updates in controllers:

```php
// app/Listeners/HandleNewUpdate.php
use Serogaq\TgBotApi\Facades\BotManager;
use Serogaq\TgBotApi\Events\NewUpdateEvent;
use Serogaq\TgBotApi\Traits\ProcessingInControllers;
class HandleNewUpdate {
	use ProcessingInControllers;
}
```

Next, create controller for the update:

```bash
$ php artisan make:tgbotapi:controller CommandUpdate
```

All updates with type CommandUpdate, i.e. commands, will be processed in the controller:

```php
// app/TgBotApi/Updates/CommandUpdate.php
use Serogaq\TgBotApi\Events\NewUpdateEvent;
use Serogaq\TgBotApi\Facades\BotManager;
use Serogaq\TgBotApi\Interfaces\UpdateController;
class CommandUpdate implements UpdateController {
	public function __construct(protected NewUpdateEvent $event) {}
	public function handle(): void {
		BotManager::bot('username_bot')?->sendMessage([
			'text' => $this->event->update['message']['text'],
			'chat_id' => $this->event->update['message']['chat']['id']
		])->send();
	}
}
```

### Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING][link-contributing] for details.

### Troubleshooting

If you like living on the edge, please report any bugs you find on the
[issues][link-issues] page.

## License

The BSD-3-Clause. Please see [License File][link-license] for more information.

[link-repo]: https://github.com/serogaq/laravel-tgbotapi
[link-issues]: https://github.com/serogaq/laravel-tgbotapi/issues
[link-license]: https://github.com/serogaq/laravel-tgbotapi/blob/v1/LICENSE.md
[link-contributing]: https://github.com/serogaq/laravel-tgbotapi/blob/v1/CONTRIBUTING.md