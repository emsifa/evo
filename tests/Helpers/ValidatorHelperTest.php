<?php

namespace Emsifa\Evo\Tests\Helpers;

use DateTime;
use Emsifa\Evo\Helpers\ValidatorHelper;
use Emsifa\Evo\Rules\Exists;
use Emsifa\Evo\Rules\Required;
use Emsifa\Evo\Rules\SameWith;
use Emsifa\Evo\Tests\Samples\MockPresenceVerifier;
use Emsifa\Evo\Tests\Samples\ObjectToValidate;
use Emsifa\Evo\Tests\TestCase;
use Emsifa\Evo\ValidationData;
use ReflectionClass;
use ReflectionProperty;

class ValidatorHelperTest extends TestCase
{
    public function testGetScalarRules()
    {
        $reflection = new ReflectionClass(ObjectToValidate::class);
        $myInt = $reflection->getProperty('myInt');
        $myFloat = $reflection->getProperty('myFloat');
        $myBool = $reflection->getProperty('myBool');
        $myString = $reflection->getProperty('myString');

        $this->assertEquals(['myInt' => ['numeric']], ValidatorHelper::getRulesFromParameterOrProperty($myInt));
        $this->assertEquals(['myFloat' => ['numeric']], ValidatorHelper::getRulesFromParameterOrProperty($myFloat));
        $this->assertEquals(['myString' => ['string']], ValidatorHelper::getRulesFromParameterOrProperty($myString));
        $this->assertEquals(['myBool' => ['boolean']], ValidatorHelper::getRulesFromParameterOrProperty($myBool));

        // test with alias
        $this->assertEquals(['x' => ['numeric']], ValidatorHelper::getRulesFromParameterOrProperty($myInt, 'x'));
    }

    public function testGetArrayRules()
    {
        $reflection = new ReflectionClass(ObjectToValidate::class);
        $myMixedArray = $reflection->getProperty('myMixedArray');
        $myArrayOfInt = $reflection->getProperty('myArrayOfInt');
        $childs = $reflection->getProperty('childs');

        $this->assertEquals(['myMixedArray' => ['array']], ValidatorHelper::getRulesFromParameterOrProperty($myMixedArray));

        $this->assertEquals([
            'myArrayOfInt' => ['array'],
            'myArrayOfInt.*' => ['numeric'],
        ], ValidatorHelper::getRulesFromParameterOrProperty($myArrayOfInt));

        $this->assertEquals([
            'childs' => ['array'],
            'childs.*.id' => ['numeric'],
            'childs.*.name' => ['string'],
        ], ValidatorHelper::getRulesFromParameterOrProperty($childs));

        // test with alias
        $this->assertEquals([
            'x' => ['array'],
            'x.*.id' => ['numeric'],
            'x.*.name' => ['string'],
        ], ValidatorHelper::getRulesFromParameterOrProperty($childs, 'x'));
    }

    public function testGetRulesFromPropertyToPropertyWithAttributeRulesShouldReturnRulesFromAttributes()
    {
        $property = new ReflectionProperty(ObjectToValidate::class, 'attrRules');

        $data = new ValidationData([]);
        $presenceVerifier = new MockPresenceVerifier(collect([]));
        $rules = ValidatorHelper::getRulesFromParameterOrProperty(
            $property,
            data: $data,
            presenceVerifier: $presenceVerifier,
        );

        $this->assertInstanceOf(Required::class, $rules['attrRules'][1]);
        $this->assertInstanceOf(SameWith::class, $rules['attrRules'][2]);
        $this->assertInstanceOf(Exists::class, $rules['attrRules'][3]);
    }

    public function testGetRulesFromTypeNameShouldReturnExpectedRule()
    {
        $this->assertEquals(["numeric"], ValidatorHelper::getRulesFromTypeName('int'));
        $this->assertEquals(["numeric"], ValidatorHelper::getRulesFromTypeName('float'));
        $this->assertEquals(["string"], ValidatorHelper::getRulesFromTypeName('string'));
        $this->assertEquals(["boolean"], ValidatorHelper::getRulesFromTypeName('bool'));
        $this->assertEquals(["date"], ValidatorHelper::getRulesFromTypeName(DateTime::class));
        $this->assertEquals([], ValidatorHelper::getRulesFromTypeName('qwertyfoobar'));
    }
}
