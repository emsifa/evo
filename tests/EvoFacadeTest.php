<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Evo;
use Emsifa\Evo\EvoFacade;
use Illuminate\Routing\Router;

class EvoFacadeTest extends TestCase
{
    /**
     * @test
     */
    public function testGetFacadeRootShouldReturnSameEvoInstance()
    {
        $evo = new Evo($this->app->make(Router::class), $this->app);
        $this->app->bind('evo', fn () => $evo);

        $result1 = EvoFacade::getFacadeRoot();
        $result2 = EvoFacade::getFacadeRoot();

        $this->assertTrue($evo === $result1);
        $this->assertTrue($evo === $result2);
    }
}
