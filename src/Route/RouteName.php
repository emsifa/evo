<?php

namespace Emsifa\Evo\Route;

use Attribute;
use Emsifa\Evo\Contracts\RouteModifier;

#[Attribute(Attribute::TARGET_CLASS + Attribute::TARGET_METHOD)]
class RouteName implements RouteModifier
{
    /**
     * @param  string[] $name
     * @return void
     */
    public function __construct(
        protected string $name,
        protected string $separator = '.',
    ) {}

    public function modifyRoute(Route $route)
    {
        if ($name = $route->getName()) {
            $route->setAction(array_merge(
                $route->getAction(),
                ['as' => $this->name . $this->separator . $name],
            ));
        }
    }
}
