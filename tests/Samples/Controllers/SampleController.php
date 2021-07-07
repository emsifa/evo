<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Route\Delete;
use Emsifa\Evo\Route\Get;
use Emsifa\Evo\Route\Patch;
use Emsifa\Evo\Route\Post;
use Emsifa\Evo\Route\Put;
use Emsifa\Evo\Route\RoutePrefix;
use Illuminate\Routing\Controller;

#[RoutePrefix('sample')]
class SampleController extends Controller
{
    #[Get]
    public function get()
    {
    }

    #[Get('stuff')]
    public function getStuff()
    {
    }

    #[Post('stuff')]
    public function postStuff()
    {
    }

    #[Put('stuff')]
    public function putStuff()
    {
    }

    #[Patch('stuff')]
    public function patchStuff()
    {
    }

    #[Delete('stuff')]
    public function deleteStuff()
    {
    }
}
