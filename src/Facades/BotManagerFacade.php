<?php

namespace Serogaq\TgBotApi\Facades;

use Illuminate\Support\Facades\Facade;

use Serogaq\TgBotApi\BotManager as BM;

class BotManager extends Facade {
    protected static function getFacadeAccessor() {
        return BM::class;
    }
}
