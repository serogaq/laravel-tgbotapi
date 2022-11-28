<?php

namespace Serogaq\TgBotApi\Tests\Unit\Services\OffsetStore;

use Serogaq\TgBotApi\Services\OffsetStore\FileOffsetStore;
use Serogaq\TgBotApi\Tests\TestCase;

/**
 * @coversDefaultClass \Serogaq\TgBotApi\Services\OffsetStore\FileOffsetStore
 */
class FileOffsetStoreTest extends TestCase {

    protected $offsetStore;

    public function setUp(): void {
        parent::setUp();
        $this->offsetStore = new FileOffsetStore();
    }

    /**
     * @test
     * @coverage ::set
     * @coverage ::get
     * @coverage ::flush
     * @coverage ::createOffsetFileIfNotExists
     */
    public function offset_storage_must_store_offset() {
        $botId = 11111111;
        $this->offsetStore->flush($botId);
        $this->assertEquals(0, $this->offsetStore->get($botId));
        $this->offsetStore->set($botId, 123456789);
        $this->assertEquals(123456789, $this->offsetStore->get($botId));
        $this->offsetStore->flush($botId);
        $this->assertEquals(0, $this->offsetStore->get($botId));
    }
}
