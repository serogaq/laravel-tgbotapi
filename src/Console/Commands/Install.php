<?php

namespace Serogaq\TgBotApi\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Install extends Command {
    /**
     * The signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tgbotapi:install {--force}';

    protected $description = 'Install laravel-tgbotapi package';

    public function handle() {
        $force = $this->option('force');
        if ($this->alreadyInstalled() && $force !== true) {
            $confirmOverwrite = $this->confirm('Package laravel-tgbotapi already installed. Do you want to overwrite config, update listener?', false);
            if ($confirmOverwrite === false) {
                $this->info('Reinstall canceled');
                return 0;
            }
            $force = true;
        }
        if ($force) {
            $confirmOverwrite = true;
        }
        if (isset($confirmOverwrite) && $confirmOverwrite === true) {
            $this->info('Reinstalling laravel-tgbotapi package...');
        } else {
            $this->info('Installing laravel-tgbotapi package...');
        }
        if (!$this->configExists('tgbotapi.php') || $confirmOverwrite) {
            $this->info('Publishing configuration...');
            $this->publish('tgbotapi-config', $force);
        }
        if (!$this->listenerExists('HandleNewUpdate.php') || $confirmOverwrite) {
            $this->info('Publishing listener...');
            $this->publishUpdateListener();
        }
        $this->info('Installed laravel-tgbotapi package');
    }

    private function configExists($fileName) {
        return File::exists(config_path($fileName));
    }

    private function listenerExists($fileName) {
        return File::exists(app_path("Listeners/{$fileName}"));
    }

    private function alreadyInstalled(): bool {
        return $this->configExists('tgbotapi.php');
    }

    private function publish($tag, $forcePublish = false) {
        $params = [
            '--provider' => 'Serogaq\TgBotApi\Providers\TgBotApiServiceProvider',
            '--tag' => $tag,
        ];
        if ($forcePublish === true) {
            $params['--force'] = true;
        }
        return $this->call('vendor:publish', $params);
    }

    private function publishUpdateListener() {
        $this->call('make:tgbotapi:listener', ['name' => 'HandleNewUpdate']);
    }
}
