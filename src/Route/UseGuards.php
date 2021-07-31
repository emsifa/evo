<?php

namespace Emsifa\Evo\Route;

use Attribute;
use Emsifa\Evo\Contracts\RouteModifier;

#[Attribute(Attribute::TARGET_CLASS + Attribute::TARGET_METHOD)]
class UseGuards implements RouteModifier
{
    protected array $guards = [];

    public function __construct(string ...$guards)
    {
        $this->guards = $guards;
    }

    public function modifyRoute(Route $route)
    {
        $guards = implode(",", $this->getGuardsArray());
        $route->middleware("auth:{$guards}");
    }

    public function getGuardsArray(): array
    {
        return $this->guards ?: [config('auth.defaults.guard')];
    }
}
