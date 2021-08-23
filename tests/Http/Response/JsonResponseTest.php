<?php

namespace Emsifa\Evo\Tests\Http\Response;

use Emsifa\Evo\Tests\Samples\Responses\SampleJsonResponse;
use Emsifa\Evo\Tests\TestCase;
use ReflectionMethod;

class JsonResponseTest extends TestCase
{
    public function testItShouldReturnCorrectJsonData()
    {
        $jsonResponse = new SampleJsonResponse();
        $jsonResponse->id = 10;
        $jsonResponse->title = "Lorem Ipsum";

        $this->assertEquals([
            "id" => 10,
            "title" => "Lorem Ipsum",
        ], $jsonResponse->getJsonData());
    }

    public function testGetJsonTemplateShouldReturnNullIfDoesntHaveJsonTemplate()
    {
        $jsonResponse = new SampleJsonResponse();
        $jsonResponse->id = 10;
        $jsonResponse->title = "Lorem Ipsum";

        $getJsonTemplate = new ReflectionMethod($jsonResponse, "getJsonTemplate");
        $getJsonTemplate->setAccessible(true);

        $this->assertEquals(null, $getJsonTemplate->invoke($jsonResponse));
    }
}
