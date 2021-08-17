<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\RequiredWith;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class RequiredWithTest extends TestCase
{
    public function testItShouldBeRequiredIfOtherValueIsNotEmpty()
    {
        $data = [
            'foo' => '',
            'bar' => '12',
        ];

        $rule = new RequiredWith('bar');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertTrue($validator->fails(), "Validation should be fails because foo is empty while bar is not empty.");
    }

    public function testItShouldBeRequiredIfOneOfOtherValueIsNotEmpty()
    {
        $data = [
            'foo' => '',
            'bar' => '',
            'baz' => '13',
        ];

        $rule = new RequiredWith(['bar', 'baz']);
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertTrue($validator->fails(), "Validation should be fails because foo is empty while baz is not empty.");
    }

    public function testItShouldNotRequiredIfOtherValueIsExists()
    {
        $data = [
            'foo' => '',
            'bar' => '',
        ];

        $rule = new RequiredWith('bar');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertFalse($validator->fails(), "Validation should be passes even foo is empty, because bar is empty.");
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new RequiredWith('foo', $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new RequiredWith('foo');

        $this->assertEquals(__('validation.required_with', ['values' => 'foo']), $rule->message());
    }
}
