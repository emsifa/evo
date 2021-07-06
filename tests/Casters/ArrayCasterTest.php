<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Casters\ArrayCaster;
use Emsifa\Evo\Exceptions\CastErrorException;
use Emsifa\Evo\Tests\Samples\ChildObject;
use Emsifa\Evo\Tests\Samples\SampleArray;
use ReflectionProperty;

class ArrayCasterTest extends TestCase
{
    public function testCastMixedArray()
    {
        $prop = new ReflectionProperty(SampleArray::class, 'mixedArray');
        $input = [null, "1", 2, 3];
        $caster = new ArrayCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals($input, $output);
    }

    public function testCastMixedArrayWithNonArrayShouldError()
    {
        $this->expectException(CastErrorException::class);

        $prop = new ReflectionProperty(SampleArray::class, 'mixedArray');
        $input = "foo";
        $caster = new ArrayCaster;
        $caster->cast($input, $prop);
    }

    public function testCastArrayOfInt()
    {
        $prop = new ReflectionProperty(SampleArray::class, 'arrayOfInt');
        $input = ["1", 2, 3, "4.5"];
        $caster = new ArrayCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals([1,2,3,4], $output);
    }

    public function testCastArrayOfIntContainsNonIntShouldError()
    {
        $this->expectException(CastErrorException::class);

        $prop = new ReflectionProperty(SampleArray::class, 'arrayOfInt');
        $input = ["1", 2, "three", "4.5"];
        $caster = new ArrayCaster;
        $caster->cast($input, $prop);
    }

    public function testCastArrayOfIntThrownErrorShouldNotErrorIfAllCastable()
    {
        $prop = new ReflectionProperty(SampleArray::class, 'arrayOfIntThrownError');
        $input = ["1", 2, 3, "4.5"];
        $caster = new ArrayCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals([1,2,3,4], $output);
    }

    public function testCastArrayOfIntThrownErrorShouldErrorIfHasError()
    {
        $this->expectException(CastErrorException::class);

        $prop = new ReflectionProperty(SampleArray::class, 'arrayOfIntThrownError');
        $input = ["1", 2, "three", "4.5"];
        $caster = new ArrayCaster;
        $caster->cast($input, $prop);
    }

    public function testCastArrayOfIntSkipErrorShouldSkipOnError()
    {
        $prop = new ReflectionProperty(SampleArray::class, 'arrayOfIntSkipError');
        $input = ["1", 2, "three", "4.5"];
        $caster = new ArrayCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals([1,2,4], $output);
    }

    public function testCastArrayOfIntNullOnErrorErrorShouldContainsNullIfError()
    {
        $prop = new ReflectionProperty(SampleArray::class, 'arrayOfIntNullOnError');
        $input = ["1", 2, "three", "4.5"];
        $caster = new ArrayCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals([1,2,null,4], $output);
    }

    public function testCastArrayOfIntKeepAsIs()
    {
        $prop = new ReflectionProperty(SampleArray::class, 'arrayOfIntKeepAsIs');
        $input = ["1", 2, "three", "4.5"];
        $caster = new ArrayCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals([1,2,"three",4], $output);
    }

    public function testCastArrayOfChildObject()
    {
        $prop = new ReflectionProperty(SampleArray::class, 'childs');
        $input = [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
            ['id' => 3, 'name' => 'baz'],
        ];
        $caster = new ArrayCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals([
            new ChildObject(id: 1, name: 'foo'),
            new ChildObject(id: 2, name: 'bar'),
            new ChildObject(id: 3, name: 'baz'),
        ], $output);
    }
}
