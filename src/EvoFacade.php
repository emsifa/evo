<?php

namespace Emsifa\Evo;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Emsifa\Evo\Evo routes(string $controller)
 * @method static \Emsifa\Evo\Evo swagger(string $path, mixed $middleware = null)
 *
 * @see \Emsifa\Evo\Evo
 */
class EvoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'evo';
    }
}
