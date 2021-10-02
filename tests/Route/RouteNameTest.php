<?php

namespace Emsifa\Evo\Tests\Route;

use Emsifa\Evo\Evo;
use Emsifa\Evo\Route\Route;
use Emsifa\Evo\Tests\Samples\Controllers\RouteNameTestController;
use Emsifa\Evo\Tests\Samples\Controllers\RouteNameTestWithSeparatorController;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Routing\Router;

class RouteNameTest extends TestCase
{
    public function testRouteNameShouldAddNamePrefixToEveryRoutesInController()
    {
        /**
         * @var Router
         */
        $router = $this->app->make(Router::class);
        $evo = new Evo($router, $this->app);
        $evo->routes(RouteNameTestController::class);

        /**
         * @var Route $index
         * @var Route $show
         * @var Route $store
         * @var Route $ignored
         */
        [$index, $show, $store, $ignored] = $router->getRoutes()->getRoutes();

        $this->assertEquals("foo.index", $index->getName());
        $this->assertEquals("foo.show", $show->getName());
        $this->assertEquals("foo.store", $store->getName());
        $this->assertNull($ignored->getName());
    }

    public function testRouteNameWithCustomSeparatorShouldAddNamePrefixWithDifferentSeparator()
    {
        /**
         * @var Router
         */
        $router = $this->app->make(Router::class);
        $evo = new Evo($router, $this->app);
        $evo->routes(RouteNameTestWithSeparatorController::class);

        /**
         * @var Route $index
         * @var Route $show
         * @var Route $store
         * @var Route $ignored
         */
        [$index, $show, $store, $ignored] = $router->getRoutes()->getRoutes();

        $this->assertEquals("foo:index", $index->getName());
        $this->assertEquals("foo:show", $show->getName());
        $this->assertEquals("foo:store", $store->getName());
        $this->assertNull($ignored->getName());
    }
}
