<?php

namespace Serogaq\TgBotApi\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Serogaq\TgBotApi\Tests\TestCase;

class InstallTgBotApiTest extends TestCase {
    /** @test */
    public function package_installation_successful() {
        if (File::exists(config_path('tgbotapi.php'))) {
            unlink(config_path('tgbotapi.php'));
        }
        if (File::exists(app_path('Listeners/UpdateProcessing.php'))) {
            unlink(app_path('Listeners/UpdateProcessing.php'));
        }
        if (!empty(realpath(storage_path('tgbotapi')))) {
            $this->_removeDirectory(storage_path('tgbotapi'));
        }

        $this->assertFalse(File::exists(config_path('tgbotapi.php')));
        $this->assertFalse(File::exists(app_path('Listeners/UpdateProcessing.php')));
        $this->assertTrue(empty(realpath(storage_path('tgbotapi'))));

        Artisan::call('tgbotapi:install');

        $this->assertTrue(File::exists(config_path('tgbotapi.php')));
        $this->assertTrue(File::exists(app_path('Listeners/UpdateProcessing.php')));
        $this->assertFalse(empty(realpath(storage_path('tgbotapi'))));

        unlink(config_path('tgbotapi.php'));
        unlink(app_path('Listeners/UpdateProcessing.php'));
    }

    /** @test */
    public function already_installed_package_the_user_may_not_want_to_reinstall() {
        File::put(config_path('tgbotapi.php'), '<?php /*TEST*/ return []; ?>');

        $this->assertEquals('<?php /*TEST*/ return []; ?>', file_get_contents(config_path('tgbotapi.php')));

        Artisan::call('tgbotapi:install', ['--inputstream' => 'no']);

        $this->assertEquals("Reinstall canceled\n", Artisan::output());
        $this->assertEquals('<?php /*TEST*/ return []; ?>', file_get_contents(config_path('tgbotapi.php')));

        unlink(config_path('tgbotapi.php'));
    }

    /** @test */
    public function already_installed_package_the_user_may_want_to_reinstall() {
        File::put(config_path('tgbotapi.php'), '<?php /*TEST*/ return []; ?>');
        if (File::exists(app_path('Listeners/UpdateProcessing.php'))) {
            unlink(app_path('Listeners/UpdateProcessing.php'));
        }

        $this->assertEquals('<?php /*TEST*/ return []; ?>', file_get_contents(config_path('tgbotapi.php')));

        Artisan::call('tgbotapi:install', ['--inputstream' => 'yes']);

        $this->assertTrue(Str::endsWith(Artisan::output(), "Installed laravel-tgbotapi package\n"));
        $this->assertEquals(file_get_contents(__DIR__ . '/../../config/tgbotapi.php'), file_get_contents(config_path('tgbotapi.php')));

        unlink(config_path('tgbotapi.php'));
        unlink(app_path('Listeners/UpdateProcessing.php'));
        $this->_removeDirectory(storage_path('tgbotapi'));
    }
}
