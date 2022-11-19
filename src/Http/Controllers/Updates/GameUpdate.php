<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

abstract class GameUpdate extends Controller {
    abstract public function handle();
}
