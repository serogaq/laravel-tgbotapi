<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

abstract class EventUpdate extends Controller {

	abstract public function handle();

}