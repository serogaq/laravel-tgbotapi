<?php

declare(strict_types=1);

namespace Serogaq\TgBotApi\Services\OffsetStore;

use Illuminate\Support\Facades\Storage;
use Serogaq\TgBotApi\Exceptions\OffsetStoreException;
use Serogaq\TgBotApi\Interfaces\OffsetStore;

class FileOffsetStore implements OffsetStore {
    protected $disk;

    public function __construct() {
        $this->disk = Storage::build([
            'driver' => 'local',
            'root' => storage_path('app'),
        ]);
    }

    public function set(int $botId, int $offset): void {
        $fileName = "tgbotapi_{$botId}.offset";
        $this->createOffsetFileIfNotExists($botId);
        $this->disk->put($fileName, (string) $offset);
    }

    public function get($botId): int {
        $fileName = "tgbotapi_{$botId}.offset";
        $this->createOffsetFileIfNotExists($botId);
        return (int) $this->disk->get($fileName);
    }

    public function flush(int $botId): void {
        $fileName = "tgbotapi_{$botId}.offset";
        $this->set($botId, 0);
    }

    protected function createOffsetFileIfNotExists(int $botId): void {
        $fileName = "tgbotapi_{$botId}.offset";
        try {
            if ($this->disk->missing($fileName)) {
                $this->disk->put($fileName, '0');
            }
        } catch (\Exception $e) { // @codeCoverageIgnoreStart
            report($e);
            throw new OffsetStoreException('Could not create offset file', 1, $e);
        } // @codeCoverageIgnoreEnd
    }
}
