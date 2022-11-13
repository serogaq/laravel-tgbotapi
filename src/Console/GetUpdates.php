<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;
use Serogaq\TgBotApi\BotManager;
use Serogaq\TgBotApi\Exceptions\BotManagerConfigException;

class GetUpdates extends Command {

	protected $hidden = false;

	protected $signature = 'tgbotapi:getupdates {username : Bot Username} {--background : Run in background} {--first-run=yes}';

	protected $description = 'Getting bot updates';

	private $bot, $username;

	public function __construct() {
		parent::__construct();
	}

	public function handle() {
		$this->username = $this->argument('username');
		$inBackground = $this->option('background');
		$isFirstRun = ($this->option('first-run') === 'yes') ? true : false;
		if(Cache::store('file')->get('tgbotapi:getupdates:forked', '0') === '1' && $isFirstRun) return 0;
		if($inBackground && $isFirstRun) {
			Cache::store('file')->put('tgbotapi:getupdates:forked', '1');
			$this->fork();
			return 0;
		}
		try {
			$bot = BotManager::selectBot($this->username);
			$this->bot = $bot;
		} catch(BotManagerConfigException $e) {
			$this->error($e->getMessage());
			return 1;
		}
		$end_time = time()+59;
		while(time() < $end_time) {
			$t = $end_time-time();
			if($t < 10 && $inBackground) {
				$this->fork();
				return 0;
			}
			$this->bot->getUpdatesAndCreateEvents(['timeout' => $t]);	
		}
		$this->fork();
	}

	public function fork(): void {
		$process = Process::fromShellCommandline("php artisan tgbotapi:getupdates {$this->username} --background --first-run=no > /dev/null 2>&1 &", base_path(), null, null, 65);
		$process->disableOutput();
		$process->start();
	}

}