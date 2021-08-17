<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\RequiredWithoutAll;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class RequiredWithoutAllTest extends TestCase
{
    public function testItShouldBeRequiredIfAllOtherValuesAreEmpty()
    {
        $data = [
            'foo' => '',
            'bar' => '12',
            'baz' => '13',
        ];

        $rule = new RequiredWithoutAll(['bar', 'baz']);
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertFalse($validator->fails(), "Validation should be passes because foo is empty while bar and baz are not empty.");
    }

    public function testItShouldNotBeRequiredIfOneOfOtherValuesIsEmpty()
    {
        $data = [
            'foo' => '',
            'bar' => '',
            'baz' => '13'
        ];

        $rule = new RequiredWithoutAll(['bar', 'baz']);
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertFalse($validator->fails(), "Validation should be passes because baz is not empty.");
    }

    public function testItShouldNotRequiredIfOtherValueIsNotEmpty()
    {
        $data = [
            'foo' => '',
            'bar' => '12',
        ];

        $rule = new RequiredWithoutAll('bar');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertFalse($validator->fails(), "Validation should be passes because bar is not empty.");
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new RequiredWithoutAll('foo', $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new RequiredWithoutAll('foo');

        $this->assertEquals(__('validation.required_without_all', ['values' => 'foo']), $rule->message());
    }
}
