<?php

namespace Emsifa\Evo\Tests;

use DateTime;
use Emsifa\Evo\Casters\DateTimeCaster;
use Emsifa\Evo\Exceptions\CastErrorException;
use Emsifa\Evo\Tests\Samples\SampleDateTime;
use ReflectionProperty;
use stdClass;

class DateTimeCasterTest extends TestCase
{
    public function testCastDateTime()
    {
        $str = "2010-10-12";
        $prop = new ReflectionProperty(SampleDateTime::class, 'date');
        $caster = new DateTimeCaster;
        $value = $caster->cast($str, $prop);
        $this->assertInstanceOf(DateTime::class, $value);
        $this->assertEquals($str, $value->format("Y-m-d"));
    }

    public function testCastDateTimeOnNullProp()
    {
        $str = null;
        $prop = new ReflectionProperty(SampleDateTime::class, 'nullableDate');
        $caster = new DateTimeCaster;
        $value = $caster->cast($str, $prop);
        $this->assertEquals(null, $value);
    }

    public function testCastDateTimeShouldError()
    {
        $this->expectException(CastErrorException::class);

        $str = "lorem ipsum dolor";
        $prop = new ReflectionProperty(SampleDateTime::class, 'date');
        $caster = new DateTimeCaster;
        $caster->cast($str, $prop);
    }
}
