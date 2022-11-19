<?php

namespace Serogaq\TgBotApi\Http\Controllers\Updates;

use Serogaq\TgBotApi\Events\NewUpdateReceived;

class Controller {
    public $bot;

    public $update;

    public $updateChannel;

    public function __construct(NewUpdateReceived $event) {
        $this->bot = $event->bot;
        $this->update = $event->update;
        $this->updateChannel = $event->updateChannel;
    }
}
