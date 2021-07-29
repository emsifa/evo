<?php

namespace Emsifa\Evo\Tests\Http\Response;

use Emsifa\Evo\Http\Response\Mock;
use Emsifa\Evo\Http\Response\ResponseMocker;
use Emsifa\Evo\Tests\Samples\Controllers\SampleMockController;
use Emsifa\Evo\Tests\Samples\Responses\SampleErrorResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleMockResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleNotFoundResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleSuccessResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleUnknownResponse;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionMethod;

class MockTest extends TestCase
{
    public function testIsSuccessResponse()
    {
        $mock = new Mock("");
        $isSuccessResponse = new ReflectionMethod($mock, "isSuccessResponse");
        $isSuccessResponse->setAccessible(true);

        $invoke = fn (string $class) => $isSuccessResponse->invokeArgs($mock, [$class]);

        $this->assertFalse($invoke("int"));
        $this->assertFalse($invoke(SampleUnknownResponse::class));
        $this->assertFalse($invoke(SampleNotFoundResponse::class));
        $this->assertFalse($invoke(SampleErrorResponse::class));
        $this->assertTrue($invoke(SampleSuccessResponse::class));
    }

    public function testGetBestCandidateClassNameWithOneOnlyReturnType()
    {
        $controller = new SampleMockController();
        $method = new ReflectionMethod($controller, "onlyOneResponse");
        $mock = new Mock("");

        $this->assertEquals(SampleUnknownResponse::class, $mock->getBestCandidateClassName($method));
    }

    public function testGetBestCandidateClassNameFromUnionTypes()
    {
        $controller = new SampleMockController();
        $method = new ReflectionMethod($controller, "unionResponse");
        $mock = new Mock("");

        $this->assertEquals(SampleSuccessResponse::class, $mock->getBestCandidateClassName($method));
    }

    public function testGetBestCandidateClassNameWithNoSuccessResponse()
    {
        $controller = new SampleMockController();
        $method = new ReflectionMethod($controller, "unionResponseWithNoSuccess");
        $mock = new Mock("");

        $this->assertEquals(SampleUnknownResponse::class, $mock->getBestCandidateClassName($method));
    }

    public function testGetMockedResponse()
    {
        $mock = new Mock("");
        $controller = new SampleMockController();
        $method = new ReflectionMethod($controller, "getMockSuccessResponse");

        $response = $mock->getMockedResponse($this->app, $method, new Request());

        $this->assertInstanceOf(SampleSuccessResponse::class, $response);
        $this->assertTrue(in_array($response->id, [1, 2, 3]));
        $this->assertTrue(in_array($response->name, ["John Doe", "Jane Doe"]));
    }
}
