<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

abstract class CommandUpdate extends Controller {

	abstract public function handle();

}