<?php

namespace Serogaq\TgBotApi\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Serogaq\TgBotApi\Bot;
use Serogaq\TgBotApi\Objects\Update;


class NewUpdateReceived {

	use Dispatchable, SerializesModels;

	public $bot;
	public $update;
	public $updateChannel;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Bot $bot, Update $update, int $updateChannel) {
		$this->bot = $bot;
		$this->update = $update;
		$this->updateChannel = $updateChannel;
	}

}
