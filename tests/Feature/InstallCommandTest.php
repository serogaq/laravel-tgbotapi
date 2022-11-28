<?php

namespace Serogaq\TgBotApi\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Serogaq\TgBotApi\Tests\TestCase;

/**
 * @coversDefaultClass \Serogaq\TgBotApi\Console\Commands\Install
 */
class InstallCommandTest extends TestCase {
    /** @test */
    public function package_installation_successful() {
        if (File::exists(config_path('tgbotapi.php'))) {
            unlink(config_path('tgbotapi.php'));
        }
        if (File::exists(app_path('Listeners/HandleNewUpdate.php'))) {
            unlink(app_path('Listeners/HandleNewUpdate.php'));
        }

        $this->assertFalse(File::exists(config_path('tgbotapi.php')));
        $this->assertFalse(File::exists(app_path('Listeners/HandleNewUpdate.php')));

        Artisan::call('tgbotapi:install');

        $this->assertTrue(File::exists(config_path('tgbotapi.php')));
        $this->assertTrue(File::exists(app_path('Listeners/HandleNewUpdate.php')));
    }

    /** @test */
    public function already_installed_package_can_be_reinstalled() {
        File::put(config_path('tgbotapi.php'), '<?php /*TEST*/ return []; ?>');
        if (File::exists(app_path('Listeners/HandleNewUpdate.php'))) {
            unlink(app_path('Listeners/HandleNewUpdate.php'));
        }

        $this->assertEquals('<?php /*TEST*/ return []; ?>', file_get_contents(config_path('tgbotapi.php')));

        Artisan::call('tgbotapi:install --force');

        $this->assertTrue(Str::endsWith(Artisan::output(), "Installed laravel-tgbotapi package\n"));
        $this->assertEquals(file_get_contents(__DIR__ . '/../../config/tgbotapi.php'), file_get_contents(config_path('tgbotapi.php')));
    }

    /** @test */
    public function it_is_possible_to_refuse_to_reinstall_an_already_installed_package() {
        File::put(config_path('tgbotapi.php'), '<?php /*TEST*/ return []; ?>');
        if (File::exists(app_path('Listeners/HandleNewUpdate.php'))) {
            unlink(app_path('Listeners/HandleNewUpdate.php'));
        }

        $this->assertEquals('<?php /*TEST*/ return []; ?>', file_get_contents(config_path('tgbotapi.php')));

        Artisan::call('tgbotapi:install --confirm-overwrite=no');

        $this->assertTrue(Str::endsWith(Artisan::output(), "Reinstall canceled\n"));
        $this->assertEquals('<?php /*TEST*/ return []; ?>', file_get_contents(config_path('tgbotapi.php')));
    }
}
