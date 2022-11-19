<?php

namespace Serogaq\TgBotApi;

use Illuminate\Support\Facades\Facade;

class TgBotApiFacade extends Facade {
    protected static function getFacadeAccessor() {
        return BotManager::class;
    }
}
