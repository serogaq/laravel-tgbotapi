<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

abstract class PreCheckoutQueryUpdate extends Controller {
    abstract public function handle();
}
