<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\RequiredIf;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class RequiredIfTest extends TestCase
{
    public function testItShouldBeRequiredIfOtherValueDoesMatchCriteria()
    {
        $data = [
            'foo' => '',
            'bar' => '10',
        ];

        $rule = new RequiredIf('bar', '10');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testItShouldNotRequiredIfOtherValueDoesNotMatchCriteria()
    {
        $data = [
            'foo' => '',
            'bar' => '10',
        ];

        $validator = Validator::make($data, [
            'foo' => [new RequiredIf('bar', '10')],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new RequiredIf('foo', 10, $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new RequiredIf('foo', 10);

        $this->assertEquals(__('validation.required_if', ['other' => 'foo', 'value' => 10]), $rule->message());
    }
}
