<?php

namespace Emsifa\Evo\Tests\Http\Response;

use Emsifa\Evo\Http\Response\ResponseType;
use Emsifa\Evo\Tests\TestCase;

class ResponseTypeTest extends TestCase
{
    public function testItShouldReturnGivenType()
    {
        $responseType = new ResponseType('application/json');
        $result = $responseType->getType();

        $this->assertEquals('application/json', $result);
    }
}
