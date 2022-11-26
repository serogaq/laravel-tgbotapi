<?php

namespace Serogaq\TgBotApi\Facades;

use Illuminate\Support\Facades\Facade;

class BotManager extends Facade {
    protected static function getFacadeAccessor() {
        return \Serogaq\TgBotApi\BotManager::class;
    }
}
