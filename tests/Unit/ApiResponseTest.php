<?php

namespace Serogaq\TgBotApi\Tests\Unit;

use Serogaq\TgBotApi\ApiResponse;
use Serogaq\TgBotApi\Exceptions\ApiResponseException;
use Serogaq\TgBotApi\Tests\TestCase;

/**
 * @coversDefaultClass \Serogaq\TgBotApi\ApiResponse
 */
class ApiResponseTest extends TestCase {

    /**
     * @test
     * @covers ::__construct
     */
    public function api_response_cannot_be_created_without_body() {
        $this->expectException(\ArgumentCountError::class);
        new ApiResponse();
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function api_response_cannot_be_created_with_empty_string_body() {
        $this->expectException(ApiResponseException::class);
        $this->expectExceptionCode(0);
        new ApiResponse('');
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function api_response_cannot_be_created_with_empty_array_body() {
        $this->expectException(ApiResponseException::class);
        $this->expectExceptionCode(1);
        new ApiResponse([]);
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function api_response_cannot_be_created_with_invalid_json_body() {
        $this->expectException(ApiResponseException::class);
        $this->expectExceptionCode(0);
        new ApiResponse('{"ok":true,{"message":[]}}');
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::asObject
     * @covers ::asJson
     */
    public function api_response_must_be_created_with_a_valid_body_from_a_string_or_array() {
        $apiResponse = new ApiResponse('{"ok":true}');
        $this->assertInstanceOf(ApiResponse::class, $apiResponse);
        $this->assertEquals('{"ok":true}', $apiResponse->asJson());
        $object = new \stdClass();
        $object->ok = true;
        $this->assertEquals($object, $apiResponse->asObject());
        $apiResponse = new ApiResponse(['ok' => false]);
        $this->assertInstanceOf(ApiResponse::class, $apiResponse);
        $this->assertEquals('{"ok":false}', $apiResponse->asJson());
        $object = new \stdClass();
        $object->ok = false;
        $this->assertEquals($object, $apiResponse->asObject());
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function api_response_object_must_optionally_be_cast_to_string() {
        $apiResponse = new ApiResponse('{"ok":true}');
        $this->assertIsString((string)$apiResponse);
    }

    /**
     * @test
     * @covers ::offsetGet
     */
    public function getting_the_value_in_api_response_array_by_key_should_work() {
        $apiResponse = new ApiResponse('{"ok":true,"message":{"text":"test"}}');
        $this->assertEquals(true, $apiResponse['ok']);
        $this->assertEquals('test', $apiResponse['message']['text']);
    }
    
    /**
     * @test
     * @covers ::offsetSet
     */
    public function changing_the_value_in_api_response_array_by_key_should_not_change_the_value() {
        $apiResponse = new ApiResponse('{"ok":true}');
        $apiResponse['ok'] = false;
        $this->assertEquals(true, $apiResponse['ok']);
    }
    
    /**
     * @test
     * @covers ::offsetUnset
     */
    public function deleting_an_element_api_response_array_should_not_delete_the_element() {
        $apiResponse = new ApiResponse('{"ok":true}');
        unset($apiResponse['ok']);
        $this->assertEquals(true, $apiResponse['ok']);
    }

    /**
     * @test
     * @covers ::offsetExists
     */
    public function checking_value_in_api_response_array_should_work() {
        $apiResponse = new ApiResponse('{"ok":true}');
        $this->assertTrue(isset($apiResponse['ok']));
        $this->assertFalse(isset($apiResponse['not-exists']));
    }
    
    /**
     * @test
     * @covers ::getRequestId
     */
    public function api_response_may_or_may_not_have_a_requese_id() {
        $apiResponse = new ApiResponse('{"ok":true}', 'a1b25cf8d5');
        $this->assertEquals('a1b25cf8d5', $apiResponse->getRequestId());
        $this->assertStringMatchesFormat('%x', $apiResponse->getRequestId());
        $apiResponse = new ApiResponse('{"ok":true}');
        $this->assertNull($apiResponse->getRequestId());
    }
}
