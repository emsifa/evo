<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Casters\FloatCaster;
use Emsifa\Evo\Exceptions\CastErrorException;
use Emsifa\Evo\Tests\Samples\SampleFloat;
use ReflectionProperty;
use stdClass;

class FloatCasterTest extends TestCase
{
    public function castableProvider()
    {
        $floatProp = new ReflectionProperty(SampleFloat::class, 'float');
        $nullableFloatProp = new ReflectionProperty(SampleFloat::class, 'nullableFloat');

        return [
            [12, $floatProp, 12.0],
            [12, $nullableFloatProp, 12.0],
            ["12", $floatProp, 12.0],
            ["12", $nullableFloatProp, 12.0],
            ["12.3", $floatProp, 12.3],
            ["12.3", $nullableFloatProp, 12.3],
            ["12.7", $floatProp, 12.7],
            ["12.7", $nullableFloatProp, 12.7],
            [null, $floatProp, 0.0],
            [null, $nullableFloatProp, null],
        ];
    }

    public function castErrorProvider()
    {
        $floatProp = new ReflectionProperty(SampleFloat::class, 'float');
        $nullableFloatProp = new ReflectionProperty(SampleFloat::class, 'nullableFloat');

        return [
            ["123 ipsum", $floatProp],
            ["123 ipsum", $nullableFloatProp],
            [new stdClass, $floatProp],
            [new stdClass, $nullableFloatProp],
            [[1], $floatProp],
            [[1], $nullableFloatProp],
            [date_create(), $floatProp],
            [date_create(), $nullableFloatProp],
        ];
    }

    /**
     * @test
     * @dataProvider castableProvider
     */
    public function testCast($value, ReflectionProperty $property, $expected)
    {
        $caster = new FloatCaster();
        $result = $caster->cast($value, $property);
        $this->assertEquals($expected, $result);

        if (is_numeric($expected)) {
            $this->assertTrue(gettype($result) === "double");
        }
    }

    /**
     * @test
     * @dataProvider castErrorProvider
     */
    public function testCastError($value, ReflectionProperty $property)
    {
        $caster = new FloatCaster();
        $this->expectException(CastErrorException::class);

        $caster->cast($value, $property);
    }
}
