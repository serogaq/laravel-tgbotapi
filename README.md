# Laravel TgBotApi

[![Latest Stable Version](http://poser.pugx.org/serogaq/laravel-tgbotapi/v)](https://packagist.org/packages/serogaq/laravel-tgbotapi)
[![License](http://poser.pugx.org/serogaq/laravel-tgbotapi/license)](https://packagist.org/packages/serogaq/laravel-tgbotapi)

This package provides methods for working with the telegram bot api, and helpers for receiving updates via webhooks or the polling method  
Tested for Laravel 8+

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

Add a listener for the new update received event:

```php
// app/Providers/EventServiceProvider.php
use App\Listeners\UpdateProcessing;
use Serogaq\TgBotApi\Events\NewUpdateReceived;
class EventServiceProvider extends ServiceProvider {
	protected $listen = [
		NewUpdateReceived::class => [
			UpdateProcessing::class,
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

Events will be sent to `app/Listeners/UpdateProcessing.php` where you can process them.

There are helpers for handling update events in controllers for each group of UpdateType's:

```php
// app/Listeners/UpdateProcessing.php
use Serogaq\TgBotApi\Traits\ProcessingInControllers;
use Serogaq\TgBotApi\Traits\{HandlingCommandUpdate, HandlingEventUpdate};
class UpdateProcessing {
	use ProcessingInControllers;
	use HandlingCommandUpdate, HandlingEventUpdate;
}
```

Next, create handler controllers for the two UpdateType's:

```bash
$ php artisan make:tgbotapi-controller CommandUpdate
$ php artisan make:tgbotapi-controller EventUpdate
```

Now you can process all events in the handle method in the corresponding controller:

```php
// app/Http/Controllers/TgBotApi/CommandUpdate.php
use Serogaq\TgBotApi\Bot;
use Serogaq\TgBotApi\Objects\UpdateType;
use Serogaq\TgBotApi\Http\Controllers\Updates\CommandUpdate as AbstractUpdate;
class CommandUpdate extends AbstractUpdate {
	public function handle() {
		# /start
		if($this->update->commandMatch('start') && $this->update->isUpdateType(UpdateType::COMMAND_WITHOUT_ARGS)) $this->startCommand($this->bot, $this->update->message->chat->id);
		# /start args
		if($this->update->commandMatch('start') && $this->update->isUpdateType(UpdateType::COMMAND_WITH_ARGS_SPACE)) $this->startCommand($this->bot, $this->update->message->chat->id, $this->update->getMatches());
	}
	public function startCommand(Bot $bot, int $chat_id, ?string $start = null) {
		$bot->sendMessage(['text' => 'Hello!', 'parse_mode' => 'HTML', 'chat_id' => $chat_id]);
	}

}
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Troubleshooting

If you like living on the edge, please report any bugs you find on the
[issues](https://github.com/serogaq/laravel-tgbotapi/issues) page.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
