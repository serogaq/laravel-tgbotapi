<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Interfaces;

interface Update {
    public function asObject(): object;

    public function __toString(): string;
}
