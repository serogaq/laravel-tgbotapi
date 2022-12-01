<?php

namespace Serogaq\TgBotApi\Tests\Unit\Services\HttpClient;

use Serogaq\TgBotApi\Services\HttpClient\LaravelHttpClient;
use Illuminate\Support\Facades\Cache;
use Serogaq\TgBotApi\ApiResponse;
use Serogaq\TgBotApi\Exceptions\HttpClientException;
use Serogaq\TgBotApi\Tests\TestCase;

/**
 * @coversDefaultClass \Serogaq\TgBotApi\Services\HttpClient\LaravelHttpClient
 */
class LaravelHttpClientTest extends TestCase {

    public function setUp(): void {
        parent::setUp();
    }

    /**
     * @test
     * @covers ::setRequestId
     * @covers ::getRequestId
     */
    public function http_client_get_set_request_id() {
        $httpClient = new LaravelHttpClient();
        $httpClient->setRequestId('a1b25cf8d5');
        $this->assertEquals('a1b25cf8d5', $httpClient->getRequestId());
    }

    /**
     * @test
     * @covers ::setRequestHash
     * @covers ::getRequestHash
     */
    public function http_client_get_set_request_hash() {
        $httpClient = new LaravelHttpClient();
        $httpClient->setRequestHash('a1f4a27a3469419ac8f2fbab7f969e1a');
        $this->assertEquals('a1f4a27a3469419ac8f2fbab7f969e1a', $httpClient->getRequestHash());
    }

    /**
     * @test
     * @covers ::setTimeout
     * @covers ::getTimeout
     */
    public function http_client_get_set_timeout() {
        $httpClient = new LaravelHttpClient();
        $httpClient->setTimeout(30);
        $this->assertEquals(30, $httpClient->getTimeout());
    }

    /**
     * @test
     * @covers ::setConnectTimeout
     * @covers ::getConnectTimeout
     */
    public function http_client_get_set_connect_timeout() {
        $httpClient = new LaravelHttpClient();
        $httpClient->setConnectTimeout(2);
        $this->assertEquals(2, $httpClient->getConnectTimeout());
    }

    /**
     * @test
     * @covers ::fake
     */
    public function http_client_fake_api_response() {
        $httpClient = new LaravelHttpClient();
        $apiResponse = new ApiResponse('{"ok":true}', 200);
        $httpClient->setRequestId('a1b25cf8d5')->setRequestHash('a1f4a27a3469419ac8f2fbab7f969e1a')->fake($apiResponse);
        $key = 'tgbotapi_httpclientfake_' . $httpClient->getRequestHash();
        $this->assertTrue(Cache::has($key));
        $req = json_decode(Cache::get($key), true, \JSON_THROW_ON_ERROR);
        $this->assertEquals('ApiResponse', $req['type']);
        $this->assertEquals('{"ok":true}', $req['body']);
        $this->assertEquals(200, $req['statusCode']);
        $this->assertEquals('a1b25cf8d5', $req['requestId']);
    }

    /**
     * @test
     * @covers ::fake
     */
    public function http_client_fake_http_client_exception() {
        $httpClient = new LaravelHttpClient();
        $exception = new HttpClientException('test', 10);
        $httpClient->setRequestId('a1b25cf8d5')->setRequestHash('a1f4a27a3469419ac8f2fbab7f969e1a')->fake($exception);
        $key = 'tgbotapi_httpclientfake_' . $httpClient->getRequestHash();
        $this->assertTrue(Cache::has($key));
        $exc = json_decode(Cache::get($key), true, \JSON_THROW_ON_ERROR);
        $this->assertEquals('HttpClientException', $exc['type']);
        $this->assertEquals('test', $exc['message']);
        $this->assertEquals(10, $exc['code']);
        $this->assertEquals('a1b25cf8d5', $exc['requestId']);
    }

    /**
     * @test
     * @covers ::send
     */
    public function http_client_send_get_request_should_return_api_response() {
        $httpClient = new LaravelHttpClient();
        $response = $httpClient->setRequestId('a1b25cf8d5')
            ->setRequestHash('a1f4a27a3469419ac8f2fbab7f969e1a')
            ->send(
                url: 'https://httpstat.us/200',
                method: 'GET'
            );
        $this->assertInstanceOf(ApiResponse::class, $response);
    }

    /**
     * @test
     * @covers ::send
     */
    public function http_client_send_get_request_should_throw_http_client_exception_with_code_1() {
        $this->expectException(HttpClientException::class);
        $this->expectExceptionCode(1);
        $httpClient = new LaravelHttpClient();
        $response = $httpClient->setRequestId('a1b25cf8d5')
            ->setRequestHash('a1f4a27a3469419ac8f2fbab7f969e1a')
            ->send(
                url: 'https://ewfwefwefergj34iughewfiu23hfuwfbf23bfi23fbrbgi2b3fiub23gb3g.com',
                method: 'GET'
            );
    }

    /**
     * @test
     * @covers ::send
     */
    public function http_client_send_get_request_should_throw_http_client_exception_with_code_2() {
        $this->expectException(HttpClientException::class);
        $this->expectExceptionCode(2);
        $httpClient = new LaravelHttpClient();
        $response = $httpClient->setRequestId('a1b25cf8d5')
            ->setRequestHash('a1f4a27a3469419ac8f2fbab7f969e1a')
            ->send(
                url: 'https://httpstat.us/400',
                method: 'GET'
            );
    }

    /**
     * @test
     * @covers ::send
     */
    public function http_client_send_post_request_should_return_api_response() {
        $httpClient = new LaravelHttpClient();
        $response = $httpClient->setRequestId('a1b25cf8d5')
            ->setRequestHash('a1f4a27a3469419ac8f2fbab7f969e1a')
            ->send(
                url: 'https://httpstat.us/200',
                method: 'POST',
                data: ['test' => true, 'data' => ['key' => 'val']],
                files: [
                    ['file', 'content', 'file.txt']
                ],
            );
        $this->assertInstanceOf(ApiResponse::class, $response);
    }

    /**
     * @test
     * @covers ::send
     */
    public function http_client_send_post_request_should_throw_http_client_exception_with_code_1() {
        $this->expectException(HttpClientException::class);
        $this->expectExceptionCode(1);
        $httpClient = new LaravelHttpClient();
        $response = $httpClient->setRequestId('a1b25cf8d5')
            ->setRequestHash('a1f4a27a3469419ac8f2fbab7f969e1a')
            ->send(
                url: 'https://ewfwefwefergj34iughewfiu23hfuwfbf23bfi23fbrbgi2b3fiub23gb3g.com',
                method: 'POST'
            );
    }

    /**
     * @test
     * @covers ::send
     */
    public function http_client_send_post_request_should_throw_http_client_exception_with_code_2() {
        $this->expectException(HttpClientException::class);
        $this->expectExceptionCode(2);
        $httpClient = new LaravelHttpClient();
        $response = $httpClient->setRequestId('a1b25cf8d5')
            ->setRequestHash('a1f4a27a3469419ac8f2fbab7f969e1a')
            ->send(
                url: 'https://httpstat.us/400',
                method: 'POST'
            );
    }
}
