<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Evo;
use Emsifa\Evo\Tests\Samples\Controllers\SampleController;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use ReflectionMethod;

class EvoTest extends TestCase
{
    /**
     * @test
     */
    public function testGetRoutesFromController()
    {
        $container = new Container;
        $evo = new Evo(new Router(new Dispatcher($container)), $container);
        $getRoutesFromController = new ReflectionMethod($evo, 'getRoutesFromController');
        $getRoutesFromController->setAccessible(true);

        /**
         * @var Route[]
         */
        $routes = $getRoutesFromController->invokeArgs($evo, [SampleController::class]);

        $this->assertEquals(6, count($routes));

        $this->assertEquals('sample', $routes[0]->uri);
        $this->assertEquals(['GET', 'HEAD'], $routes[0]->methods());

        $this->assertEquals('sample/stuff', $routes[1]->uri);
        $this->assertEquals(['GET', 'HEAD'], $routes[1]->methods());

        $this->assertEquals('sample/stuff', $routes[2]->uri);
        $this->assertEquals(['POST'], $routes[2]->methods());

        $this->assertEquals('sample/stuff', $routes[3]->uri);
        $this->assertEquals(['PUT'], $routes[3]->methods());

        $this->assertEquals('sample/stuff', $routes[4]->uri);
        $this->assertEquals(['PATCH'], $routes[4]->methods());

        $this->assertEquals('sample/stuff', $routes[5]->uri);
        $this->assertEquals(['DELETE'], $routes[5]->methods());
    }
}
