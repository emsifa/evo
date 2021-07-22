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
    public function testToEnsureEvoBindingIsRegistered()
    {
        $app = new Application();
        $provider = new EvoServiceProvider($app);

        $provider->register();

        $evo = $app->make('evo');
        $this->assertInstanceOf(Evo::class, $evo);
    }

    /**
     * @test
     */
    public function testToEnsureEvoIsSingleton()
    {
        $app = new Application();
        $provider = new EvoServiceProvider($app);

        $provider->register();

        $evo2 = $app->make(Evo::class);
        $evo1 = $app->make('evo');
        $this->assertTrue($evo1 === $evo2);
    }
}
