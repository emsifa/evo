<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Evo;
use Emsifa\Evo\EvoServiceProvider;
use Illuminate\Foundation\Application;

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
