<?php

namespace Serogaq\TgBotApi\Tests\Unit;

use Serogaq\TgBotApi\Tests\TestCase;
use function Serogaq\TgBotApi\Helpers\{
    getBotIdFromToken,
    arrayToObject,
    isValidBotConfig
};

/**
 * @coversDefaultClass \Serogaq\TgBotApi\Helpers
 */
class HelpersTest extends TestCase {
    protected array $botConfig;

    public function setUp(): void {
        parent::setUp();
        $this->botConfig = [
            'username' => 'first_bot',
            'token' => '11111111:AAFOH-Q_VxUMvOT3L2FsTAN7DKWYJpEiSQ44',
            'middleware' => [],
            'log_channel' => 'null',
            'api_url' => null,
        ];
    }

    /** @test */
    public function all_helper_functions_exist() {
        $this->assertTrue(function_exists('\Serogaq\TgBotApi\Helpers\getBotIdFromToken'));
        $this->assertTrue(function_exists('\Serogaq\TgBotApi\Helpers\arrayToObject'));
        $this->assertTrue(function_exists('\Serogaq\TgBotApi\Helpers\isValidBotConfig'));
    }

    /**
     * @test
     * @covers \Serogaq\TgBotApi\Helpers\getBotIdFromToken
     */
    public function function_getBotIdFromToken_works_correctly() {
        $this->assertEquals(11111111, getBotIdFromToken($this->botConfig['token']));
        $this->assertNull(getBotIdFromToken(null));
        $this->assertNull(getBotIdFromToken('test'));
        $this->assertNull(getBotIdFromToken('123456:AAFOH-Q_VxUMvOT3L2FsTAN7DKWYJpEiSQ4'));
    }

    /**
     * @test
     * @covers \Serogaq\TgBotApi\Helpers\arrayToObject
     */
    public function function_arrayToObject_works_correctly() {
        $object = new \stdClass();
        $object->key = 'value';
        $object0InArray = new \stdClass();
        $object0InArray->key = 'value';
        $object1InArray = new \stdClass();
        $object1InArray->key = 'value';
        $object->array = [$object0InArray, $object1InArray];
        $this->assertEquals($object, arrayToObject([
            'key' => 'value',
            'array' => [
                ['key' => 'value'],
                ['key' => 'value']
            ]
        ]));
    }

    /**
     * @test
     * @covers \Serogaq\TgBotApi\Helpers\isValidBotConfig
     */
    public function function_isValidBotConfig_works_correctly() {
        $this->assertTrue(isValidBotConfig([
            'username' => 'first_bot',
            'token' => '11111111:AAFOH-Q_VxUMvOT3L2FsTAN7DKWYJpEiSQ44',
        ]));
        $this->assertFalse(isValidBotConfig(null));
        $this->assertFalse(isValidBotConfig([]));
        $this->assertFalse(isValidBotConfig([
            'username' => 'username',
            'username' => '11111111:AAFOH-Q_VxUMvOT3L2FsTAN7DKWYJpEiSQ44'
        ]));
        $this->assertFalse(isValidBotConfig([
            'username' => 'usernamebot',
            'username' => '111111:AAFOH-Q_VxUMvOT3L2FsTAN7DKWYJpEiSQ44'
        ]));
        $this->assertFalse(isValidBotConfig([
            'username' => 'usernamebot',
            'username' => '111111111:AAFOH-Q_VxUMvOT3L2FsT'
        ]));
    }
}
