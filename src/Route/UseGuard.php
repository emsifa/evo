<?php

namespace Emsifa\Evo\Route;

use Attribute;
use Emsifa\Evo\Contracts\RouteModifier;

#[Attribute(Attribute::TARGET_CLASS + Attribute::TARGET_METHOD)]
class UseGuard implements RouteModifier
{
    public function __construct(
        protected string|array|null $guards = null,
    )
    {
    }

    public function modifyRoute(Route $route)
    {
        $guards = implode(",", $this->getGuardsArray());
        $route->middleware("auth:{$guards}");
    }

    public function getGuardsArray(): array
    {
        if (is_null($this->guards)) {
            return [config('auth.defaults.guard')];
        }

        return (array) $this->guards;
    }
}
