<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\RequiredWithout;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class RequiredWithoutTest extends TestCase
{
    public function testItShouldBeRequiredIfOtherValueIsEmpty()
    {
        $data = [
            'foo' => '',
            'bar' => '',
        ];

        $rule = new RequiredWithout('bar');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertTrue($validator->fails(), "Validation should be fails because foo is empty while bar is empty.");
    }

    public function testItShouldBeRequiredIfOneOfOtherValueIsEmpty()
    {
        $data = [
            'foo' => '',
            'bar' => '',
            'baz' => '13'
        ];

        $rule = new RequiredWithout(['bar', 'baz']);
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertTrue($validator->fails(), "Validation should be fails because foo is empty while bar is empty.");
    }

    public function testItShouldNotRequiredIfOtherValueIsNotEmpty()
    {
        $data = [
            'foo' => '',
            'bar' => '12',
        ];

        $rule = new RequiredWithout('bar');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertFalse($validator->fails(), "Validation should be passes because foo is empty while bar is not empty.");
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new RequiredWithout('foo', $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new RequiredWithout('foo');

        $this->assertEquals(__('validation.required_without', ['values' => 'foo']), $rule->message());
    }
}
