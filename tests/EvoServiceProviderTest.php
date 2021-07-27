<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Evo;
use Emsifa\Evo\EvoServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;

class EvoServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function testToEnsureEvoBindingIsRegistered()
    {
        $evo = $this->app->make('evo');
        $this->assertInstanceOf(Evo::class, $evo);
    }

    /**
     * @test
     */
    public function testToEnsureEvoIsSingleton()
    {
        $evo2 = $this->app->make(Evo::class);
        $evo1 = $this->app->make('evo');
        $this->assertTrue($evo1 === $evo2);
    }
}
