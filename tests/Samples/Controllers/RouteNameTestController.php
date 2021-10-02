<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Route\Get;
use Emsifa\Evo\Route\Post;
use Emsifa\Evo\Route\RouteName;
use Illuminate\Routing\Controller;

#[RouteName('foo')]
class RouteNameTestController extends Controller
{
    #[Get(name: 'index')]
    public function index()
    {
    }

    #[Get('{id}', name: 'show')]
    public function show()
    {
    }

    #[Post(name: 'store')]
    public function store()
    {

    }

    #[Post('ignored')]
    public function ignored()
    {
    }
}
