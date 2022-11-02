<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

abstract class CallbackQueryUpdate extends Controller {

	abstract public function handle();

}