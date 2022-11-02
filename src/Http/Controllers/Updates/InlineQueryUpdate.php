<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

abstract class InlineQueryUpdate extends Controller {

	abstract public function handle();

}