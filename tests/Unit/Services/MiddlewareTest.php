<?php

namespace Serogaq\TgBotApi\Tests\Unit\Services;

use Illuminate\Support\Facades\Config;
use Serogaq\TgBotApi\Services\Middleware;
use Serogaq\TgBotApi\Interfaces\{ RequestMiddleware, ResponseMiddleware };
use Serogaq\TgBotApi\{ ApiRequest, ApiResponse };
use Serogaq\TgBotApi\Tests\TestCase;

/**
 * @coversDefaultClass \Serogaq\TgBotApi\Services\Middleware
 */
class MiddlewareTest extends TestCase {
    /**
     * @var Middleware
     */
    protected Middleware $middleware;

    protected array $testMiddlewares;

    public function setUp(): void {
        parent::setUp();
        Config::set('tgbotapi', [
            'bots' => [
                [
                    'username' => 'first_bot',
                    'token' => '11111111:AAFOH-Q_VxUMvOT3L2FsTAN7DKWYJpEiSQ44',
                    'middleware' => [],
                    'log_channel' => 'null',
                    'api_url' => null,
                ]
            ]
        ]);
        $this->middleware = resolve(Middleware::class);
        $this->testMiddlewares = [
            \Serogaq\TgBotApi\Tests\TestRequestMiddleware1::class,
            \Serogaq\TgBotApi\Tests\TestRequestMiddleware2::class,
            \Serogaq\TgBotApi\Tests\TestResponseMiddleware::class
        ];
    }

    /**
     * @test
     * @coverage ::applyMiddlewares
     * @coverage ::addRequestMiddleware
     * @coverage ::addResponseMiddleware
     * @coverage ::isAlreadyAdded
     * @coverage ::execRequestMiddlewares
     * @coverage ::execResponseMiddlewares
     * @coverage ::flushAll
     */
    public function middleware_instance_can_be_created() {
        $this->assertInstanceOf(Middleware::class, $this->middleware);
        $apiRequest = $this->middleware->applyMiddlewares(
            new ApiRequest(11111111, 'sendMessage', [['text' => 'Hello']]),
            $this->testMiddlewares
        );
        $this->assertEquals('Hello World', $apiRequest->getArguments()[0]['text']);
        $this->assertEquals('Hello World!', $apiRequest->getArguments()[0]['new_text']);
        $apiResponse = $this->middleware->applyMiddlewares(
            new ApiResponse(['text' => 'Hello World', 'new_text' => 'Hello World!'], $apiRequest->getRequestId()),
            $this->testMiddlewares
        );
        $this->assertArrayHasKey('test', $apiResponse->asArray());
        $this->assertEquals(true, $apiResponse['test']);
    }
}
