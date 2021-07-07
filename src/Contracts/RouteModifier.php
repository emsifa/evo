<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Route\Route;

interface RouteModifier
{
    public function modifyRoute(Route $route);
}
