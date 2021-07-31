<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Route\Get;
use Emsifa\Evo\Route\RoutePrefix;
use Emsifa\Evo\Route\UseGuards;
use Illuminate\Routing\Controller;

#[RoutePrefix('guarded')]
class SampleGuardedController extends Controller
{
    #[Get('default')]
    #[UseGuards]
    public function defaultGuard()
    {
    }

    #[Get('jwt')]
    #[UseGuards('jwt')]
    public function jwtGuard()
    {
    }

    #[Get('jwt/web')]
    #[UseGuards("jwt", "web")]
    public function webAndJwtGuard()
    {
    }
}
