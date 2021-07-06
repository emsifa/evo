<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Exceptions\UndefinedCasterException;
use Emsifa\Evo\ObjectFiller;
use Emsifa\Evo\Tests\Samples\ObjectWithOverridedCaster;
use Emsifa\Evo\Tests\Samples\SimpleNestedObject;
use Emsifa\Evo\Tests\Samples\SimpleObject;
use Emsifa\Evo\Tests\Samples\SimpleObjectWithIntCaster;
use stdClass;

class ObjectFillerTest extends TestCase
{
    /**
     * @test
     */
    public function testFillSimpleObject()
    {
        $object = new SimpleObject;
        $date = date_create();

        ObjectFiller::fillObject($object, [
            'integer' => 100,
            'float' => 100.25,
            'string' => "hello",
            'boolean' => true,
            'array' => ["1", 2, 3],
            'date' => $date,
            'mixed' => new stdClass,
        ]);

        $this->assertEquals($object->integer, 100);
        $this->assertEquals($object->float, 100.25);
        $this->assertEquals($object->string, "hello");
        $this->assertEquals($object->boolean, true);
        $this->assertEquals($object->array, ["1", 2, 3]);
        $this->assertEquals($object->mixed, new stdClass);
        $this->assertEquals($object->nullableFloat, null);
        $this->assertEquals($object->date, $date);
    }

    /**
     * @test
     */
    public function testCastWithoutCasterShouldBeError()
    {
        $object = new SimpleObject;
        $date = date_create();

        $this->expectException(UndefinedCasterException::class);

        ObjectFiller::fillObject($object, [
            'integer' => "100", // this shoule be error
            'float' => 100.25,
            'string' => "hello",
            'boolean' => true,
            'array' => ["1", 2, 3],
            'date' => $date,
            'mixed' => new stdClass,
        ]);
    }

    /**
     * @test
     */
    public function testCastWithCasterShouldNotError()
    {
        $object = new SimpleObjectWithIntCaster;
        $date = date_create();

        ObjectFiller::fillObject($object, [
            'integer' => "100",
            'float' => 100.25,
            'string' => "hello",
            'boolean' => true,
            'array' => ["1", 2, 3],
            'date' => $date,
            'mixed' => new stdClass,
        ]);

        $this->assertEquals($object->integer, 100);
        $this->assertEquals($object->float, 100.25);
        $this->assertEquals($object->string, "hello");
        $this->assertEquals($object->boolean, true);
        $this->assertEquals($object->array, ["1", 2, 3]);
        $this->assertEquals($object->mixed, new stdClass);
        $this->assertEquals($object->nullableFloat, null);
        $this->assertEquals($object->date, $date);
    }

    /**
     * @test
     */
    public function testMissingRequiredValueShouldBeError()
    {
        $object = new SimpleObject;
        $date = date_create();

        $this->expectException(UndefinedCasterException::class);

        ObjectFiller::fillObject($object, [
            'float' => 100.25,
            'string' => "hello",
            'boolean' => true,
            'array' => ["1", 2, 3],
            'date' => $date,
            'mixed' => new stdClass,
        ]);
    }

    /**
     * @test
     */
    public function testFillSimpleNestedObject()
    {
        $object = new SimpleNestedObject;

        ObjectFiller::fillObject($object, [
            'child' => [
                'number' => 10,
                'text' => 'Hello'
            ],
        ]);

        $this->assertEquals($object->child->number, 10);
        $this->assertEquals($object->child->text, 'Hello');
    }

    /**
     * @test
     */
    public function testOverridedCasterShouldUsePropertyCaster()
    {
        $object = new ObjectWithOverridedCaster;

        ObjectFiller::fillObject($object, [
            'number' => "120"
        ]);

        $this->assertEquals($object->number, 60);
    }
}
