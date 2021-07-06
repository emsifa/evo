<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Casters\BoolCaster;
use Emsifa\Evo\Tests\Samples\SampleBool;
use ReflectionProperty;
use stdClass;

class BoolCasterTest extends TestCase
{
    public function castableProvider()
    {
        $boolProp = new ReflectionProperty(SampleBool::class, 'bool');
        $nullableBoolProp = new ReflectionProperty(SampleBool::class, 'nullableBool');

        return [
            ["lorem ipsum", $boolProp, true],
            ["lorem ipsum", $nullableBoolProp, true],
            [12, $boolProp, true],
            [12, $nullableBoolProp, true],
            [12.34, $boolProp, true],
            [12.34, $nullableBoolProp, true],
            [true, $boolProp, true],
            [true, $nullableBoolProp, true],
            [0, $boolProp, false],
            [0, $nullableBoolProp, false],
            ["", $boolProp, false],
            ["", $nullableBoolProp, false],
            [false, $boolProp, false],
            [false, $nullableBoolProp, false],
            [null, $boolProp, false],
            [null, $nullableBoolProp, null],
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
