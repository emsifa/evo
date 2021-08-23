<?php

namespace Emsifa\Evo\Tests\Http\Middlewares;

use Emsifa\Evo\Http\Middlewares\AddMockHeader;
use Emsifa\Evo\Http\Response\Mock;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AddMockHeaderTest extends TestCase
{
    public function testItShouldAddEvoMockHeaderToResponse()
    {
        $this->app->bind(Mock::class, function () {
            $mock = new Mock();
            $mock->setClassName("FooBar");

            return $mock;
        });

        $middleware = new AddMockHeader($this->app);
        /**
         * @var Response
         */
        $response = $middleware->handle(new Request(), function () {
            return new Response();
        });

        $mockName = $response->headers->get('Evo-Mock');
        $this->assertEquals("FooBar", $mockName);
    }
}
