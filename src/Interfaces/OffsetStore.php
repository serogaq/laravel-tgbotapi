<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Interfaces;

interface OffsetStore {
    public function set(int $botId, int $offset): void;

    public function get(int $botId): int;

    public function flush(int $botId): void;
}
