<?php

namespace Serogaq\TgBotApi\Tests;

use Serogaq\TgBotApi\Providers\TgBotApiServiceProvider;

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
}
