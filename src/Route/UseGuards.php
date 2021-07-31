<?php

namespace Emsifa\Evo\Route;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiOperationModifier;
use Emsifa\Evo\Contracts\RouteModifier;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Operation;
use Emsifa\Evo\Swagger\OpenApi\Schemas\SecurityRequirement;

#[Attribute(Attribute::TARGET_CLASS + Attribute::TARGET_METHOD)]
class UseGuards implements RouteModifier, OpenApiOperationModifier
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

    public function modifyOpenApiOperation(Operation $operation)
    {
        if (! $operation->security) {
            $operation->security = [];
        }

        foreach ($this->getGuardsArray() as $guard) {
            $operation->security[$guard] = new SecurityRequirement($guard, []);
        }
    }

    protected function getGuardsArray(): array
    {
        return $this->guards ?: [config('auth.defaults.guard')];
    }
}
