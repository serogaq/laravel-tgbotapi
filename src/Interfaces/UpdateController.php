<?php

namespace Serogaq\TgBotApi\Interfaces;

use Serogaq\TgBotApi\Events\NewUpdateEvent;

interface UpdateController {
    public function __construct(NewUpdateEvent $event);

    public function handle(): void;
}
