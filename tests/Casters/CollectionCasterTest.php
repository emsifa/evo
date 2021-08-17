<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Casters\CollectionCaster;
use Emsifa\Evo\Exceptions\CastErrorException;
use Emsifa\Evo\Tests\Samples\SampleCollection;
use ReflectionProperty;

class CollectionCasterTest extends TestCase
{
    public function testCastMixedCollection()
    {
        $prop = new ReflectionProperty(SampleCollection::class, 'mixedCollection');
        $input = [null, "1", 2, 3];
        $caster = new CollectionCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals(collect($input), $output);
    }

    public function testCastMixedCollectionWithNonCollectionShouldError()
    {
        $this->expectException(CastErrorException::class);

        $prop = new ReflectionProperty(SampleCollection::class, 'mixedCollection');
        $input = "foo";
        $caster = new CollectionCaster;
        $caster->cast($input, $prop);
    }

    public function testCastCollectionOfInt()
    {
        $prop = new ReflectionProperty(SampleCollection::class, 'collectionOfInt');
        $input = ["1", 2, 3, "4.5"];
        $caster = new CollectionCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals(collect([1,2,3,4]), $output);
    }

    public function testCastCollectionOfIntContainsNonIntShouldError()
    {
        $this->expectException(CastErrorException::class);

        $prop = new ReflectionProperty(SampleCollection::class, 'collectionOfInt');
        $input = ["1", 2, "three", "4.5"];
        $caster = new CollectionCaster;
        $caster->cast($input, $prop);
    }

    public function testCastCollectionOfIntThrownErrorShouldNotErrorIfAllCastable()
    {
        $prop = new ReflectionProperty(SampleCollection::class, 'collectionOfIntThrownError');
        $input = ["1", 2, 3, "4.5"];
        $caster = new CollectionCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals(collect([1,2,3,4]), $output);
    }

    public function testCastCollectionOfIntThrownErrorShouldErrorIfHasError()
    {
        $this->expectException(CastErrorException::class);

        $prop = new ReflectionProperty(SampleCollection::class, 'collectionOfIntThrownError');
        $input = ["1", 2, "three", "4.5"];
        $caster = new CollectionCaster;
        $caster->cast($input, $prop);
    }

    public function testCastCollectionOfIntSkipErrorShouldSkipOnError()
    {
        $prop = new ReflectionProperty(SampleCollection::class, 'collectionOfIntSkipError');
        $input = ["1", 2, "three", "4.5"];
        $caster = new CollectionCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals(collect([1,2,4]), $output);
    }

    public function testCastCollectionOfIntNullOnErrorErrorShouldContainsNullIfError()
    {
        $prop = new ReflectionProperty(SampleCollection::class, 'collectionOfIntNullOnError');
        $input = ["1", 2, "three", "4.5"];
        $caster = new CollectionCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals(collect([1,2,null,4]), $output);
    }

    public function testCastCollectionOfIntKeepAsIs()
    {
        $prop = new ReflectionProperty(SampleCollection::class, 'collectionOfIntKeepAsIs');
        $input = ["1", 2, "three", "4.5"];
        $caster = new CollectionCaster;
        $output = $caster->cast($input, $prop);
        $this->assertEquals(collect([1,2,"three",4]), $output);
    }

    public function testCastNullablePropertyWithNullValueShouldReturnsNull()
    {
        $prop = new ReflectionProperty(SampleCollection::class, 'nullableCollection');
        $input = null;
        $caster = new CollectionCaster;
        $result = $caster->cast($input, $prop);

        $this->assertNull($result);
    }
}
