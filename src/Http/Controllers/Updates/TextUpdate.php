<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

abstract class TextUpdate extends Controller {
    abstract public function handle();
}
