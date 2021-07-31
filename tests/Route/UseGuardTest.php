<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Evo;
use Emsifa\Evo\Tests\Samples\Controllers\SampleGuardedController;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use ReflectionMethod;

class UseGuardTest extends TestCase
{
    /**
     * @test
     */
    public function testGetRoutesFromController()
    {
        config([
            'auth' => [
                'defaults' => [
                    'guard' => 'web',
                ],
            ],
        ]);

        $container = $this->app;
        $evo = new Evo(new Router(new Dispatcher($container)), $container);
        $getRoutesFromController = new ReflectionMethod($evo, 'getRoutesFromController');
        $getRoutesFromController->setAccessible(true);

        /**
         * @var Route[]
         */
        $routes = $getRoutesFromController->invokeArgs($evo, [SampleGuardedController::class]);

        $this->assertEquals('guarded/default', $routes[0]->uri);
        $this->assertEquals(['auth:web'], $routes[0]->middleware());

        $this->assertEquals('guarded/jwt', $routes[1]->uri);
        $this->assertEquals(['auth:jwt'], $routes[1]->middleware());

        $this->assertEquals('guarded/jwt/web', $routes[2]->uri);
        $this->assertEquals(['auth:jwt,web'], $routes[2]->middleware());
    }
}
