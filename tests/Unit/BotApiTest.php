<?php

namespace Serogaq\TgBotApi\Tests\Unit;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Serogaq\TgBotApi\BotApi;
use Serogaq\TgBotApi\Events\NewUpdateEvent;
use Serogaq\TgBotApi\Exceptions\{ BotApiException, HttpClientException };
use Serogaq\TgBotApi\Interfaces\OffsetStore;
use Serogaq\TgBotApi\Tests\TestCase;
use Serogaq\TgBotApi\{ ApiRequest, ApiResponse };

/**
 * @coversDefaultClass \Serogaq\TgBotApi\BotApi
 */
class BotApiTest extends TestCase {
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

    /**
     * @test
     * @covers ::__construct
     */
    public function bot_api_cant_be_created_without_config() {
        $this->expectException(\ArgumentCountError::class);
        $botApi = new BotApi();
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function bot_api_cannot_be_created_with_invalid_config() {
        $this->expectException(BotApiException::class);
        $this->expectExceptionCode(0);
        $botApi = new BotApi([]);
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function bot_api_must_be_created_with_correct_config() {
        $botApi = new BotApi($this->botConfig);
        $this->assertInstanceOf(BotApi::class, $botApi);
    }

    /**
     * @test
     * @covers ::getBotId
     */
    public function bot_api_should_return_bot_id() {
        $botApi = new BotApi($this->botConfig);
        $this->assertEquals(11111111, $botApi->getBotId());
    }

    /**
     * @test
     * @covers ::createApiRequest
     */
    public function create_request_method_must_return_an_object() {
        Config::set('tgbotapi', ['bots' => [$this->botConfig]]);
        $botApi = new BotApi($this->botConfig);
        $this->assertInstanceOf(ApiRequest::class, $botApi->createApiRequest('getMe', []));
    }

    /**
     * @test
     * @covers ::__call
     */
    public function calling_a_non_existing_function_must_create_a_request_object() {
        Config::set('tgbotapi', ['bots' => [$this->botConfig]]);
        $botApi = new BotApi($this->botConfig);
        $this->assertInstanceOf(ApiRequest::class, $botApi->getMe());
        $this->assertInstanceOf(ApiRequest::class, $botApi->sendMessage());
    }

    /**
     * @test
     * @covers ::getUpdatesAndCreateEvents
     * @covers \Serogaq\TgBotApi\Services\HttpClient\LaravelHttpClient::send
     */
    public function helper_function_getUpdatesAndCreateEvents_must_create_events() {
        $eventReceived = false;
        Event::listen(NewUpdateEvent::class, function (NewUpdateEvent $event) use (&$eventReceived) {
            $this->assertInstanceOf(NewUpdateEvent::class, $event);
            $this->assertEquals('test', $event->update['message']['text']);
            $eventReceived = true;
        });
        Config::set('tgbotapi', ['bots' => [$this->botConfig]]);
        $botApi = new BotApi($this->botConfig);
        $botApi->getUpdates(['timeout' => 1, 'offset' => 0])->withFakeResponse(new ApiResponse([
            'ok' => true,
            'result' => [
                [
                    'update_id' => 1,
                    'message' => ['text' => 'test'],
                ],
            ],
        ]));
        $offsetStore = resolve(OffsetStore::class);
        $offsetStore->set($botApi->getBotId(), 0);
        $botApi->getUpdatesAndCreateEvents(['timeout' => 1]);
        $this->assertTrue($eventReceived);
    }

    /**
     * @test
     * @covers ::getUpdatesAndCreateEvents
     * @covers \Serogaq\TgBotApi\Services\HttpClient\LaravelHttpClient::send
     */
    public function helper_function_getUpdatesAndCreateEvents_may_fail() {
        $this->expectException(HttpClientException::class);
        $this->expectExceptionCode(2);
        $this->expectExceptionMessage("HTTP request returned status code 400:\n{\"ok\":false,\"error_code\":400,\"description\":\"Error\"}");
        Config::set('tgbotapi', ['bots' => [$this->botConfig]]);
        $botApi = new BotApi($this->botConfig);
        $botApi->getUpdates(['timeout' => 1, 'offset' => 0])->withFakeResponse(
            new HttpClientException(
                "HTTP request returned status code 400:\n{\"ok\":false,\"error_code\":400,\"description\":\"Error\"}",
                2,
                new ApiResponse(
                    [
                        'ok' => false,
                        'error_code' => 400,
                        'description' => 'Error',
                    ],
                    400
                )
            )
        );
        $botApi->getUpdatesAndCreateEvents(['timeout' => 1, 'offset' => 0]);
    }
}
