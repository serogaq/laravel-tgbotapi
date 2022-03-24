<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Serogaq\TgBotApi\BotManager;
use Serogaq\TgBotApi\Exceptions\BotManagerConfigException;

class GetUpdates extends Command {

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
		$end_time = time()+58;
		while(true) {
			$t = $end_time-time();
			if($t <= 2) break;
			$this->bot->getUpdatesAndCreateEvents(['timeout' => $t]);
		}
	}

}