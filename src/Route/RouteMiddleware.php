<?php

namespace Emsifa\Evo\Route;

use Attribute;
use Emsifa\Evo\Contracts\RouteModifier;

#[Attribute(Attribute::TARGET_CLASS + Attribute::TARGET_METHOD)]
class RouteMiddleware implements RouteModifier
{
    /**
     * @var string[]
     */
    protected array $middlewares;

    /**
     * @param  string[] $middlewares
     * @return void
     */
    public function __construct(...$middlewares)
    {
        $this->middlewares = $middlewares;
    }

    public function modifyRoute(Route $route)
    {
        $route->middleware($this->middlewares);
    }
}
