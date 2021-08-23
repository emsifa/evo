<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Dto\FakesCount;

class FakesCountTest extends TestCase
{
    public function testGetCountWithExactNumber()
    {
        $fakesCount = new FakesCount(10);
        $this->assertEquals(10, $fakesCount->getCount());
    }

    public function testGetCountWithMinAndMax()
    {
        $fakesCount = new FakesCount(1, 10);
        $count = $fakesCount->getCount();
        $this->assertTrue($count <= 10 && $count >= 1);
    }
}
