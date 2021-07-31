<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Route\Get;
use Emsifa\Evo\Route\RoutePrefix;
use Emsifa\Evo\Route\UseGuard;
use Illuminate\Routing\Controller;

#[RoutePrefix('guarded')]
class SampleGuardedController extends Controller
{
    #[Get('default')]
    #[UseGuard]
    public function defaultGuard()
    {
    }

    #[Get('jwt')]
    #[UseGuard('jwt')]
    public function jwtGuard()
    {
    }

    #[Get('jwt/web')]
    #[UseGuard(['jwt', 'web'])]
    public function webAndJwtGuard()
    {
    }
}
