<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallTgBotApi extends Command {

	protected $hidden = true;

	protected $signature = 'tgbotapi:install {--inputstream=}';

	protected $description = 'Install laravel-tgbotapi package';

	private $reInstall = false;

	public function handle() {
		$inputstream = is_null($this->option('inputstream')) ? null : explode('|', $this->option('inputstream'));
		if($this->alreadyInstalled()) {
			if(isset($inputstream[0])) $c = $inputstream[0] === 'yes' ? true : false;
			else $c = $this->confirm('Package laravel-tgbotapi already installed. Do you want to overwrite config, listeners, and storage?', false);
			if($c === false) {
				$this->info('Reinstall canceled');
				return 0;
			}
			$this->info('Reinstalling laravel-tgbotapi package...');
			$this->reInstall = true;
		}

		if(!$this->reInstall) $this->info('Installing laravel-tgbotapi package...');

		if(!$this->configExists('tgbotapi.php') || $this->reInstall) {
			$this->info('Publishing configuration...');
			$this->publish('tgbotapi-config', $this->reInstall);
		}

		/*$this->info('Publishing migrations...');
		if(!$this->migrationExists('.php')) {
			$this->publish('tgbotapi-migrations');
		}*/

		if(!$this->listenerExists('UpdateProcessing.php') || $this->reInstall) {
			if($this->reInstall && $this->listenerExists('UpdateProcessing.php')) $overwrite = $this->confirm('Do you want to overwrite UpdateProcessing Listener ('.app_path('Listeners/UpdateProcessing.php').') ?', false);
			if(isset($overwrite) && $overwrite === false) unset($overwrite);
			else {
				if($this->reInstall && $this->listenerExists('UpdateProcessing.php')) unlink(app_path('Listeners/UpdateProcessing.php'));
				$this->info('Publishing listener...');
				$this->publishListener('UpdateProcessing');
			}
		}

		$this->info('Creating storage...');
		if(empty(realpath(storage_path('tgbotapi'))) || $this->reInstall) {
			if($this->reInstall) $this->removeDirectory(storage_path('tgbotapi'));
			mkdir(storage_path('tgbotapi'));
			File::put(storage_path('tgbotapi/.gitignore'), "*\n!.gitignore\n");
			$this->info('Creating complete.');
		}

		$this->info('Installed laravel-tgbotapi package');
		return 0;
	}

	private function configExists($fileName) {
		return File::exists(config_path($fileName));
	}

	private function migrationExists($fileName) {
		return File::exists(database_path("migrations/$fileName"));
	}

	private function listenerExists($fileName) {
		return File::exists(app_path("Listeners/$fileName"));
	}

	private function alreadyInstalled(): bool {
		return $this->configExists('tgbotapi.php');
	}

	private function publish($tag, $forcePublish = false) {
		$params = [
			'--provider' => "Serogaq\TgBotApi\TgBotApiServiceProvider",
			'--tag' => $tag,
		];
		if($forcePublish === true) $params['--force'] = true;
		return $this->call('vendor:publish', $params);
	}

	private function publishListener($name) {
		$this->call('tgbotapi:makeupdatelistener', ['name' => $name]);
	}

	private function removeDirectory($directory) {
		$directoryPath = $directory . DIRECTORY_SEPARATOR;
		$dir = scandir($directoryPath);
		$rel = ['.', '..'];
		$files = array_diff($dir, $rel);
		foreach($files as $file) {
			$path = $directoryPath . $file;
			if(is_file($path)) unlink($path);
			elseif(is_dir($path)) $this->removeDirectory($path);
		}
		if(is_dir($directory = rtrim($directory, '\\/'))) {
			if(is_link($directory)) unlink($directory);
			else rmdir($directory);
		}
	}

}