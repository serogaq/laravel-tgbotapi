<?php

namespace Serogaq\TgBotApi\Tests;

use Serogaq\TgBotApi\TgBotApiServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase {
    public function setUp(): void {
        parent::setUp();
    }

    protected function getPackageProviders($app) {
        return [
            TgBotApiServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app) {
        //
    }

    protected function _removeDirectory($directory) {
        $directoryPath = $directory . DIRECTORY_SEPARATOR;
        $dir = scandir($directoryPath);
        $rel = ['.', '..'];
        $files = array_diff($dir, $rel);
        foreach ($files as $file) {
            $path = $directoryPath . $file;
            if (is_file($path)) {
                unlink($path);
            } elseif (is_dir($path)) {
                $this->_removeDirectory($path);
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
