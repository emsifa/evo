<?php

namespace Emsifa\Evo\Tests\Helpers;

use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Rules\In;
use Emsifa\Evo\Rules\NotIn;
use Emsifa\Evo\Rules\Required;
use Emsifa\Evo\Tests\Samples\Dto\ReflectionHelperDto;
use Emsifa\Evo\Tests\TestCase;
use ReflectionMethod;
use ReflectionProperty;

class ReflectionHelperTest extends TestCase
{
    public function testHasAttributeShouldReturnTrueIfNameExistsInArray()
    {
        $property = new ReflectionProperty(ReflectionHelperDto::class, 'thing');

        $result = ReflectionHelper::hasAttribute($property, [In::class, Required::class]);

        $this->assertEquals(true, $result);
    }

    public function testHasAttributeShouldReturnFalseIfNameDoesNotExistsInArray()
    {
        $property = new ReflectionProperty(ReflectionHelperDto::class, 'thing');

        $result = ReflectionHelper::hasAttribute($property, [In::class, NotIn::class]);

        $this->assertEquals(false, $result);
    }

    public function testUnionHasTypeShouldReturnFalseIfTypeDoesNotExists()
    {
        $property = new ReflectionProperty(ReflectionHelperDto::class, 'number');

        $union = $property->getType();
        $result = ReflectionHelper::unionHasType($union, 'string');

        $this->assertEquals(false, $result);
    }

    public function testHasDefaultValue()
    {
        $method = new ReflectionMethod(ReflectionHelperDto::class, 'method');
        $parameters = $method->getParameters();

        $paramNoDefault = $parameters[0];
        $paramWithDefault = $parameters[1];
        $propNoDefault = new ReflectionProperty(ReflectionHelperDto::class, 'noDefaultValue');
        $propWithDefault = new ReflectionProperty(ReflectionHelperDto::class, 'withDefaultValue');

        $this->assertFalse(ReflectionHelper::hasDefaultValue($paramNoDefault));
        $this->assertFalse(ReflectionHelper::hasDefaultValue($propNoDefault));
        $this->assertTrue(ReflectionHelper::hasDefaultValue($paramWithDefault));
        $this->assertTrue(ReflectionHelper::hasDefaultValue($propWithDefault));
    }

    public function testGetDefaultValue()
    {
        $method = new ReflectionMethod(ReflectionHelperDto::class, 'method');
        $parameters = $method->getParameters();

        $paramWithDefault = $parameters[1];
        $propWithDefault = new ReflectionProperty(ReflectionHelperDto::class, 'withDefaultValue');

        $this->assertEquals(10, ReflectionHelper::getDefaultValue($paramWithDefault));
        $this->assertEquals("lorem ipsum", ReflectionHelper::getDefaultValue($propWithDefault));
    }
}
