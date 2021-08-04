<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Casters\BoolCaster;
use Emsifa\Evo\Tests\Samples\SampleBool;
use ReflectionProperty;

class BoolCasterTest extends TestCase
{
    public function castableProvider()
    {
        $boolProp = new ReflectionProperty(SampleBool::class, 'bool');
        $nullableBoolProp = new ReflectionProperty(SampleBool::class, 'nullableBool');

        return [
            [null, $boolProp, false],
            [null, $nullableBoolProp, null],
            [true, $boolProp, true],
            [true, $nullableBoolProp, true],
            ["true", $boolProp, true],
            ["true", $nullableBoolProp, true],
            [false, $boolProp, false],
            [false, $nullableBoolProp, false],
            ["false", $boolProp, false],
            ["false", $nullableBoolProp, false],
            [1, $boolProp, true],
            [1, $nullableBoolProp, true],
            ["1", $boolProp, true],
            ["1", $nullableBoolProp, true],
            [0, $boolProp, false],
            [0, $nullableBoolProp, false],
            ["0", $boolProp, false],
            ["0", $nullableBoolProp, false],
        ];
    }

    /**
     * @test
     * @dataProvider castableProvider
     */
    public function testCast($value, ReflectionProperty $property, $expected)
    {
        $caster = new BoolCaster();
        $result = $caster->cast($value, $property);
        $this->assertEquals($expected, $result);
    }
}
