<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Serogaq\TgBotApi\BotManager;
use Serogaq\TgBotApi\Exceptions\BotManagerConfigException;
use Resolute\PseudoDaemon\IsPseudoDaemon;

class GetUpdates extends Command {

	use IsPseudoDaemon;

	protected $hidden = false;

	protected $signature = 'tgbotapi:getupdates {username : Bot Username}';

	protected $description = 'Getting bot updates';

	private $bot;

	public function __construct() {
		parent::__construct();
	}

	public function handle() {
		$username = $this->argument('username');
		try {
			$bot = BotManager::selectBot($username);
			$this->bot = $bot;
		} catch(BotManagerConfigException $e) {
			$this->error($e->getMessage());
			return 1;
		}
		$this->runAsPseudoDaemon();
	}

	public function process() {
		while(true) {
			$this->bot->getUpdatesAndCreateEvents(['timeout' => 59]);
		}
	}

	public function pseudoDaemonSleepSeconds() {
		return 1;
	}

}