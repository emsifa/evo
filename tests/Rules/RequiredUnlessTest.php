<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\RequiredUnless;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class RequiredUnlessTest extends TestCase
{
    public function testItShouldBeRequiredIfOtherValueDoesNotMatchCriteria()
    {
        $data = [
            'foo' => '',
            'bar' => '12'
        ];

        $rule = new RequiredUnless('bar', '10');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testItShouldNotRequiredIfOtherValueDoesMatchCriteria()
    {
        $data = [
            'foo' => '',
            'bar' => '10'
        ];

        $rule = new RequiredUnless('bar', '10');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new RequiredUnless('foo', 10, $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new RequiredUnless('foo', 10);

        $this->assertEquals(__('validation.required_unless', ['other' => 'foo', 'value' => 10]), $rule->message());
    }
}
