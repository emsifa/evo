<?php

namespace Emsifa\Evo;

use Emsifa\Evo\Contracts\RouteModifier;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Route\Route;
use Emsifa\Evo\Swagger\SwaggerController;
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
     * @param  string $path
     * @param  string|array|null $middleware
     * @return void
     */
    public function swagger(string $path = 'swagger', $middleware = null)
    {
        $showUi = $this->router->get($path, [SwaggerController::class, 'showUi'])->name('evo:swagger-ui');
        $openApi = $this->router->get("{$path}/openapi", [SwaggerController::class, 'openApi'])->name('evo:openapi');

        if ($middleware) {
            $showUi->middleware($middleware);
            $openApi->middleware($middleware);
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
        $classRouteModifiers = ReflectionHelper::getAttributesInstances($reflection, RouteModifier::class, ReflectionAttribute::IS_INSTANCEOF);

        $methods = $reflection->getMethods();
        foreach ($methods as $method) {
            $methodRoutes = ReflectionHelper::getAttributesInstances($method, Route::class, ReflectionAttribute::IS_INSTANCEOF);
            $routeModifiers = array_merge(
                $classRouteModifiers,
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
