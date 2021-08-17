<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Required;
use Illuminate\Support\Facades\Validator;

class RequiredTest extends TestCase
{
    public function testItShouldBeValidWhenValueIsNotEmpty()
    {
        $data = [
            'foo' => 'bar',
        ];

        $validator = Validator::make($data, [
            'foo' => [new Required],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenKeyDoesNotExists()
    {
        $data = [];

        $validator = Validator::make($data, [
            'foo' => [new Required],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenValueIsEmpty()
    {
        $data = [
            'foo' => '',
        ];

        $rule = new Required;

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new Required($message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new Required();

        $this->assertEquals(__('validation.required'), $rule->message());
    }
}
