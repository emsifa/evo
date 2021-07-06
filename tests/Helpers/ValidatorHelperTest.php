<?php

namespace Emsifa\Evo\Tests\Helpers;

use Emsifa\Evo\Helpers\ValidatorHelper;
use Emsifa\Evo\Tests\Samples\ObjectToValidate;
use Emsifa\Evo\Tests\TestCase;
use ReflectionClass;

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
            'myArrayOfInt.*' => ['numeric']
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
}
