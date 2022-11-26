<?php

namespace Serogaq\TgBotApi\Tests\Unit;

use Serogaq\TgBotApi\BotApi;
use Serogaq\TgBotApi\BotManager;
use Serogaq\TgBotApi\Tests\TestCase;

class BotManagerTest extends TestCase {
    protected array $botsConfig;

    /**
     * @var BotManager
     */
    protected BotManager $botManager;

    public function setUp(): void {
        parent::setUp();
        $this->botsConfig = [
            [
                'username' => 'first_bot',
                'token' => '11111111:WwwWwwwWwwWw1wWwW221Www2',
                'middleware' => [],
                'log_channel' => 'null',
                'api_url' => null,
            ], [
                'username' => 'second_bot',
                'token' => '2222222222:QqqQqq21QQqqqq11-11_qwwwq',
                'middleware' => [],
                'log_channel' => 'null',
                'api_url' => null,
            ],
        ];
        $this->botManager = new BotManager($this->botsConfig);
    }

    /** @test */
    public function bot_manager_can_be_created_without_config() {
        $botManager = new BotManager();
        $this->assertInstanceOf(BotManager::class, $botManager);
    }

    /** @test */
    public function bot_must_be_configured_before_it_can_be_used() {
        $botManager = new BotManager();
        $this->assertNull($botManager->bot('testbot'));
    }

    /** @test */
    public function bot_manager_should_receive_bots_from_config() {
        $this->assertTrue($this->botManager->botExists('first_bot'));
        $this->assertTrue($this->botManager->botExists(2222222222));
        $this->assertFalse($this->botManager->botExists('non_exists_bot'));
    }

     /** @test */
     public function bot_manager_should_return_bot_api_class() {
         $this->assertInstanceOf(BotApi::class, $this->botManager->bot('first_bot'));
     }

    /** @test */
    public function bot_manager_must_return_config_of_an_existing_bot() {
        $this->assertEquals($this->botManager->getBotConfig(11111111), $this->botsConfig[0]);
        $this->assertEquals($this->botManager->getBotConfig('second_bot'), $this->botsConfig[1]);
        $this->assertNull($this->botManager->getBotConfig('non_exists_bot'));
    }
}
