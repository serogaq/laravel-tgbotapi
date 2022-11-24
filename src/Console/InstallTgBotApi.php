<?php

namespace Serogaq\TgBotApi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallTgBotApi extends Command {
    protected $hidden = false;

    protected $signature = 'tgbotapi:install {--force}';

    protected $description = 'Install laravel-tgbotapi package';

    private $reInstall = false;

    public function handle() {
        $force = $this->option('force');
        if ($this->alreadyInstalled() && $force !== true) {
            $confirmOverwrite = $this->confirm('Package laravel-tgbotapi already installed. Do you want to overwrite config?', false);
            if ($confirmOverwrite === false) {
                $this->info('Reinstall canceled');
                return 0;
            }
            $force = true;
        }
        if($force) $confirmOverwrite = true;
        if(isset($confirmOverwrite) && $confirmOverwrite === true) {
            $this->info('Reinstalling laravel-tgbotapi package...');
        } else {
            $this->info('Installing laravel-tgbotapi package...');
        }
        //
        if (!$this->configExists('tgbotapi.php') || $confirmOverwrite) {
            $this->info('Publishing configuration...');
            $this->publish('tgbotapi-config', $force);
        }
        //
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
            '--provider' => "Serogaq\TgBotApi\Providers\TgBotApiServiceProvider",
            '--tag' => $tag,
        ];
        if ($forcePublish === true) {
            $params['--force'] = true;
        }
        return $this->call('vendor:publish', $params);
    }

    /*private function publishListener($name) {
        $this->call('tgbotapi:makeupdatelistener', ['name' => $name]);
    }*/

    private function removeDirectory($directory) {
        $directoryPath = $directory . DIRECTORY_SEPARATOR;
        $dir = scandir($directoryPath);
        $rel = ['.', '..'];
        $files = array_diff($dir, $rel);
        foreach ($files as $file) {
            $path = $directoryPath . $file;
            if (is_file($path)) {
                unlink($path);
            } elseif (is_dir($path)) {
                $this->removeDirectory($path);
            }
        }
        if (is_dir($directory = rtrim($directory, '\\/'))) {
            if (is_link($directory)) {
                unlink($directory);
            } else {
                rmdir($directory);
            }
        }
    }
}
