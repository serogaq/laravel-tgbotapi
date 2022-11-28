<?php

namespace Serogaq\TgBotApi\Tests\Unit;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Serogaq\TgBotApi\Exceptions\{ ApiRequestException, HttpClientException };
use Serogaq\TgBotApi\Tests\TestCase;
use Serogaq\TgBotApi\{ ApiRequest, ApiResponse };

/**
 * @coversDefaultClass \Serogaq\TgBotApi\ApiRequest
 */
class ApiRequestTest extends TestCase {
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
                ],
            ],
        ]);
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function api_request_cannot_be_created_without_bot_id() {
        $this->expectException(\ArgumentCountError::class);
        $apiRequest = new ApiRequest(method: 'getMe', arguments: []);
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function api_request_cannot_be_created_with_invalid_bot_id() {
        $this->expectException(ApiRequestException::class);
        $this->expectExceptionCode(0);
        $apiRequest = new ApiRequest(1, method: 'getMe', arguments: []);
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function api_request_can_be_created_without_arguments_parameter() {
        $apiRequest = new ApiRequest(11111111, 'getMe');
        $this->assertInstanceOf(ApiRequest::class, $apiRequest);
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function api_request_object_must_optionally_be_cast_to_string() {
        $apiRequest = new ApiRequest(11111111, 'getMe');
        $this->assertIsString((string) $apiRequest);
    }

    /**
     * @test
     * @covers ::getRequestId
     */
    public function each_api_request_must_have_an_id_after_creation() {
        $apiRequest = new ApiRequest(11111111, 'getMe');
        $this->assertStringMatchesFormat('%x', $apiRequest->getRequestId());
    }

    /**
     * @test
     * @covers ::withFakeResponse
     */
    public function api_request_function_withFakeResponse_should_work() {
        $apiRequest = new ApiRequest(11111111, 'getMe');
        $requestHash = $apiRequest->withFakeResponse(new ApiResponse(['ok' => true]))->getRequestHash();
        $req = json_decode(Cache::get("tgbotapi_httpclientfake_{$requestHash}"), true, \JSON_THROW_ON_ERROR);
        $this->assertEquals('{"ok":true}', $req['body']);
        $apiRequest = new ApiRequest(11111111, 'getMe');
        $requestHash = $apiRequest->withFakeResponse(new HttpClientException('test exception', -1))->getRequestHash();
        $req = json_decode(Cache::get("tgbotapi_httpclientfake_{$requestHash}"), true, \JSON_THROW_ON_ERROR);
        $this->assertEquals('HttpClientException', $req['type']);
    }

    /**
     * @test
     * @covers ::getBotId
     */
    public function api_request_function_getBotId_should_return_bot_id() {
        $apiRequest = new ApiRequest(11111111, 'getMe');
        $this->assertEquals(11111111, $apiRequest->getBotId());
    }

    /**
     * @test
     * @covers ::getMethod
     */
    public function api_request_function_getMethod_should_return_method() {
        $apiRequest = new ApiRequest(11111111, 'getMe');
        $this->assertEquals('getMe', $apiRequest->getMethod());
    }

    /**
     * @test
     * @covers ::getArguments
     */
    public function api_request_function_getArguments_should_return_arguments() {
        $apiRequest = new ApiRequest(11111111, 'getMe');
        $this->assertEquals([], $apiRequest->getArguments());
        $apiRequest = new ApiRequest(11111111, 'getMe', [['ok' => true]]);
        $this->assertEquals([['ok' => true]], $apiRequest->getArguments());
    }

    /**
     * @test
     * @covers ::getRequestHash
     */
    public function api_request_function_getRequestHash_should_return_hash() {
        $apiRequest = new ApiRequest(11111111, 'getMe');
        $this->assertEquals('a1f4a27a3469419ac8f2fbab7f969e1a', $apiRequest->getRequestHash());
        $apiRequest = new ApiRequest(11111111, 'getUpdates', [['timeout' => 50, 'offset' => 10]]);
        $this->assertEquals('1458e57c38d7e1d4af1a55165c116ca7', $apiRequest->getRequestHash());
    }

    /**
     * @test
     * @covers ::send
     */
    public function api_request_function_send_should_work() {
        (new ApiRequest(11111111, 'getMe'))->withFakeResponse(new ApiResponse(['ok' => true]));
        $apiResponse = (new ApiRequest(11111111, 'getMe'))->send();
        $this->assertInstanceOf(ApiResponse::class, $apiResponse);
        $this->assertEquals('{"ok":true}', $apiResponse->asJson());
    }
}
