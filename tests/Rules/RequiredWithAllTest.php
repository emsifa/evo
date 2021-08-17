<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\RequiredWithAll;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class RequiredWithAllTest extends TestCase
{
    public function testItShouldBeRequiredIfAllOtherValuesIsNotEmpty()
    {
        $data = [
            'foo' => '',
            'bar' => '12',
            'baz' => '13',
        ];

        $rule = new RequiredWithAll(['bar', 'baz']);
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertTrue($validator->fails(), "Validation should be fails because foo is empty while bar and baz are not empty.");
    }

    public function testItShouldNotBeRequiredIfOneOfOtherValuesIsEmpty()
    {
        $data = [
            'foo' => '',
            'bar' => '',
            'baz' => '13',
        ];

        $rule = new RequiredWithAll(['bar', 'baz']);
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertFalse($validator->fails(), "Validation should be passes because bar is empty.");
    }

    public function testItShouldNotRequiredIfOtherValueIsEmpty()
    {
        $data = [
            'foo' => '',
            'bar' => '',
        ];

        $rule = new RequiredWithAll('bar');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertFalse($validator->fails(), "Validation should be passes even foo is empty, because bar is empty.");
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new RequiredWithAll('foo', $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new RequiredWithAll('foo');

        $this->assertEquals(__('validation.required_with_all', ['values' => 'foo']), $rule->message());
    }
}
