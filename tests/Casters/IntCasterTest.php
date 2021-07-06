<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Casters\IntCaster;
use Emsifa\Evo\Exceptions\CastErrorException;
use Emsifa\Evo\Tests\Samples\SampleInt;
use ReflectionProperty;
use stdClass;

class IntCasterTest extends TestCase
{
    public function castableProvider()
    {
        $intProp = new ReflectionProperty(SampleInt::class, 'int');
        $nullableIntProp = new ReflectionProperty(SampleInt::class, 'nullableInt');

        return [
            [12, $intProp, 12],
            [12, $nullableIntProp, 12],
            ["12", $intProp, 12],
            ["12", $nullableIntProp, 12],
            ["12.3", $intProp, 12],
            ["12.3", $nullableIntProp, 12],
            ["12.7", $intProp, 12],
            ["12.7", $nullableIntProp, 12],
            [null, $intProp, 0],
            [null, $nullableIntProp, null],
        ];
    }

    public function castErrorProvider()
    {
        $intProp = new ReflectionProperty(SampleInt::class, 'int');
        $nullableIntProp = new ReflectionProperty(SampleInt::class, 'nullableInt');

        return [
            ["123 ipsum", $intProp],
            ["123 ipsum", $nullableIntProp],
            [new stdClass, $intProp],
            [new stdClass, $nullableIntProp],
            [[1], $intProp],
            [[1], $nullableIntProp],
            [date_create(), $intProp],
            [date_create(), $nullableIntProp],
        ];
    }

    /**
     * @test
     * @dataProvider castableProvider
     */
    public function testCast($value, ReflectionProperty $property, $expected)
    {
        $caster = new IntCaster();
        $result = $caster->cast($value, $property);
        $this->assertEquals($expected, $result);

        if (is_int($expected)) {
            $this->assertTrue(gettype($result) === "integer");
        }
    }

    /**
     * @test
     * @dataProvider castErrorProvider
     */
    public function testCastError($value, ReflectionProperty $property)
    {
        $caster = new IntCaster();
        $this->expectException(CastErrorException::class);

        $caster->cast($value, $property);
    }
}
