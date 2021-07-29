<?php

namespace Emsifa\Evo\Tests\Http\Response;

use Emsifa\Evo\Http\Response\ResponseMocker;
use Emsifa\Evo\Tests\Samples\Responses\SampleMockableResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleMockResponse;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use ReflectionClass;

class ResponseMockerTest extends TestCase
{
    public function testResponseMocker()
    {
        $mocker = new ResponseMocker($this->app);
        $responseClass = SampleMockResponse::class;
        $reflectionClass = new ReflectionClass($responseClass);
        $request = new Request(request: [
            'numberFromRequest' => "123",
            'nameFromRequest' => "Nicola Tesla",
        ]);

        /**
         * @var SampleMockResponse $response
         */
        $response = $mocker->mock($reflectionClass, $request);

        $wordsCount = fn (string $str) => count(explode(" ", $str));

        $this->assertInstanceOf(SampleMockResponse::class, $response);
        $this->assertTrue($response->int >= 1 && $response->int <= 1000);
        $this->assertTrue($response->float >= 0 && $response->float <= 1000);
        $this->assertTrue($wordsCount($response->string) >= 5 && $wordsCount($response->string) <= 10);

        $this->assertEquals(123, $response->numberFromRequest);
        $this->assertEquals("Nicola Tesla", $response->nameFromRequest);

        $this->assertEquals(1, preg_match("/^[0-9A-F]+-[0-9A-F]+-[0-9A-F]+-[89AB][0-9A-F]+-[0-9A-F]+$/i", $response->uuid));
        $this->assertTrue(is_string($response->creditCardNumber) && is_numeric($response->creditCardNumber));

        $this->assertTrue($response->fakeInt >= 1500 && $response->fakeInt <= 1505);
        $this->assertTrue($response->fakeFloat >= 2000 && $response->fakeInt <= 2001);

        $this->assertTrue(in_array($response->fakeString, ["foo", "bar", "baz"]));

        $this->assertTrue($response->child->id >= 1 && $response->child->id <= 1000);
        $this->assertTrue(! preg_match("/[a-z]/i", $response->child->phoneNumber));

        $this->assertCount(7, $response->childs);
    }

    public function testMockMockableResponse()
    {
        $reflectionMockableResponse = new ReflectionClass(SampleMockableResponse::class);
        $mocker = new ResponseMocker($this->app);
        $response = $mocker->mock($reflectionMockableResponse, new Request());

        $this->assertTrue($response->int === 123);
        $this->assertTrue($response->float === 1.23);
        $this->assertTrue(in_array($response->string, ["foo", "bar", "baz"]));
        $this->assertTrue($response->bool);
    }
}
