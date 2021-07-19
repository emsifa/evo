<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Evo;
use Emsifa\Evo\EvoServiceProvider;
use Emsifa\Evo\Tests\Samples\Controllers\SampleController;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use ReflectionMethod;

class EvoServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function testEnsureEvoBindingRegistered()
    {
        $app = new Application();
        $provider = new EvoServiceProvider($app);

        $provider->register();

        $evo = $app->make('evo');
        $this->assertInstanceOf(Evo::class, $evo);
    }
}
