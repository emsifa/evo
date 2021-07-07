<?php

namespace Emsifa\Evo;

use Emsifa\Evo\Contracts\RouteModifier;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Route\Route;
use Illuminate\Container\Container;
use Illuminate\Routing\Router;
use ReflectionAttribute;
use ReflectionClass;

class Evo
{
    /**
     * @var string[]
     */
    protected array $controllers = [];

    public function __construct(
        protected Router $router,
        protected Container $container,
    ) {
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return string[]
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * @param  string $controller
     * @return void
     */
    public function routes(string $controller)
    {
        $this->controllers[] = $controller;
        $routes = $this->getRoutesFromController($controller);
        foreach ($routes as $route) {
            $this->router->getRoutes()->add($route);
        }
    }

    /**
     * @param  string $controller
     * @return Route[]
     */
    protected function getRoutesFromController(string $controller): array
    {
        $routes = [];
        $reflection = new ReflectionClass($controller);
        $routeModifiers = ReflectionHelper::getAttributesInstances($reflection, RouteModifier::class, ReflectionAttribute::IS_INSTANCEOF);

        $methods = $reflection->getMethods();
        foreach ($methods as $method) {
            $methodRoutes = ReflectionHelper::getAttributesInstances($method, Route::class, ReflectionAttribute::IS_INSTANCEOF);
            $routeModifiers = array_merge(
                $routeModifiers,
                ReflectionHelper::getAttributesInstances(
                    $method,
                    RouteModifier::class,
                    ReflectionAttribute::IS_INSTANCEOF,
                )
            );
            /**
             * @var \Emsifa\Evo\Route\Route $route
             */
            foreach ($methodRoutes as $route) {
                $route->setUses($controller.'@'.$method->getName());
                $route->setContainer($this->container);
                $routes[] = $this->applyRouteModifiers($route, $routeModifiers);
            }
        }

        return $routes;
    }

    /**
     * @param  Route $route
     * @param  RouteModifier[] $modifiers
     * @return Route
     */
    protected function applyRouteModifiers(Route $route, array $modifiers): Route
    {
        foreach ($modifiers as $modifier) {
            $modifier->modifyRoute($route);
        }

        return $route;
    }
}
