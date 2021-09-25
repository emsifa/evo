<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Evo;
use Emsifa\Evo\Swagger\SwaggerController;
use Emsifa\Evo\Tests\Samples\Controllers\SampleController;
use Emsifa\Evo\Tests\Samples\Controllers\SampleSwaggerController;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use ReflectionMethod;

class EvoTest extends TestCase
{
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

    public function testGetControllers()
    {
        $evo = new Evo($this->app->make(Router::class), $this->app);

        $evo->routes(SampleController::class);
        $evo->routes(SampleSwaggerController::class);

        $controllers = $evo->getControllers();

        $this->assertEquals([SampleController::class, SampleSwaggerController::class], $controllers);
    }

    public function testGetContainer()
    {
        $evo = new Evo($this->app->make(Router::class), $this->app);

        $this->assertEquals($this->app, $evo->getContainer());
    }

    public function testGetRouter()
    {
        $router = $this->app->make(Router::class);
        $evo = new Evo($router, $this->app);

        $this->assertEquals($router, $evo->getRouter());
    }

    public function testSwagger()
    {
        /**
         * @var Router
         */
        $router = $this->app->make(Router::class);
        $evo = new Evo($router, $this->app);

        $this->assertNull($router->getRoutes()->getByAction(SwaggerController::class.'@showUi'));
        $this->assertNull($router->getRoutes()->getByAction(SwaggerController::class.'@openApi'));

        $evo->swagger('/api/docs', middleware: 'auth');

        $this->assertNotNull($router->getRoutes()->getByAction(SwaggerController::class.'@showUi'));
        $this->assertNotNull($router->getRoutes()->getByAction(SwaggerController::class.'@openApi'));
    }

    public function testRegisterRoutesInAGroup()
    {
        /**
         * @var Router
         */
        $router = $this->app->make(Router::class);
        $evo = new Evo($router, $this->app);

        $router->group([
            'prefix' => '/foo',
            'middleware' => 'auth',
        ], function () use ($evo) {
            $evo->routes(SampleController::class);
        });

        $routes = $router->getRoutes()->getRoutes();

        $this->assertEquals("/foo/sample", $routes[0]->uri());
        $this->assertEquals(["GET", "HEAD"], $routes[0]->methods());
        $this->assertEquals(["auth"], $routes[0]->middleware());

        $this->assertEquals("/foo/sample/stuff", $routes[1]->uri());
        $this->assertEquals(["GET", "HEAD"], $routes[1]->methods());
        $this->assertEquals(["auth"], $routes[1]->middleware());

        $this->assertEquals("/foo/sample/stuff", $routes[2]->uri());
        $this->assertEquals(["POST"], $routes[2]->methods());
        $this->assertEquals(["auth"], $routes[2]->middleware());
    }

    public function testRegisterRoutesWithPrefixAndMiddleware()
    {
        /**
         * @var Router
         */
        $router = $this->app->make(Router::class);
        $evo = new Evo($router, $this->app);
        $evo->routes(SampleController::class, prefix: '/foo', middleware: 'auth');

        $routes = $router->getRoutes()->getRoutes();

        $this->assertEquals("/foo/sample", $routes[0]->uri());
        $this->assertEquals(["GET", "HEAD"], $routes[0]->methods());
        $this->assertEquals(["auth"], $routes[0]->middleware());

        $this->assertEquals("/foo/sample/stuff", $routes[1]->uri());
        $this->assertEquals(["GET", "HEAD"], $routes[1]->methods());
        $this->assertEquals(["auth"], $routes[1]->middleware());

        $this->assertEquals("/foo/sample/stuff", $routes[2]->uri());
        $this->assertEquals(["POST"], $routes[2]->methods());
        $this->assertEquals(["auth"], $routes[2]->middleware());
    }
}
