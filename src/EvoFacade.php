<?php

namespace Emsifa\Evo;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Emsifa\Evo\Evo
 */
class EvoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'evo';
    }
}
