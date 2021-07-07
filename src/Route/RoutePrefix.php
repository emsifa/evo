<?php

namespace Emsifa\Evo\Route;

use Attribute;
use Emsifa\Evo\Contracts\RouteModifier;
use Emsifa\Evo\Route\Route;

#[Attribute(Attribute::TARGET_CLASS)]
class RoutePrefix implements RouteModifier
{
    protected string $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function modifyRoute(Route $route)
    {
        $route->prefix($this->prefix);
    }
}
