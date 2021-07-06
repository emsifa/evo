<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Casters\StringCaster;
use Emsifa\Evo\Exceptions\CastErrorException;
use Emsifa\Evo\Tests\Samples\SampleString;
use ReflectionProperty;
use stdClass;

class StringCasterTest extends TestCase
{
    public function castableProvider()
    {
        $stringProp = new ReflectionProperty(SampleString::class, 'string');
        $nullableStringProp = new ReflectionProperty(SampleString::class, 'nullableString');

        return [
            ["lorem ipsum", $stringProp, "lorem ipsum"],
            ["lorem ipsum", $nullableStringProp, "lorem ipsum"],
            [12, $stringProp, "12"],
            [12, $nullableStringProp, "12"],
            [12.34, $stringProp, "12.34"],
            [12.34, $nullableStringProp, "12.34"],
            [true, $stringProp, "true"],
            [true, $nullableStringProp, "true"],
            [false, $stringProp, "false"],
            [false, $nullableStringProp, "false"],
            [null, $stringProp, ""],
            [null, $nullableStringProp, ""],
        ];
    }

    public function castErrorProvider()
    {
        $stringProp = new ReflectionProperty(SampleString::class, 'string');
        $nullableStringProp = new ReflectionProperty(SampleString::class, 'nullableString');

        return [
            [new stdClass, $stringProp],
            [new stdClass, $nullableStringProp],
            [[1,2,3], $stringProp],
            [[1,2,3], $nullableStringProp],
        ];
    }

    /**
     * @test
     * @dataProvider castableProvider
     */
    public function testCast($value, ReflectionProperty $property, $expected)
    {
        $caster = new StringCaster();
        $result = $caster->cast($value, $property);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider castErrorProvider
     */
    public function testCastError($value, ReflectionProperty $property)
    {
        $caster = new StringCaster();
        $this->expectException(CastErrorException::class);

        $caster->cast($value, $property);
    }
}
