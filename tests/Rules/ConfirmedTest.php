<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Confirmed;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class ConfirmedTest extends TestCase
{
    public function testItShouldBeValidWhenConfirmedValueIsSame()
    {
        $data = [
            'password' => 'lorem',
            'password_confirmation' => 'lorem',
        ];

        $rule = new Confirmed;
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'password' => [$rule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenConfirmedValueIsDifferent()
    {
        $data = [
            'password' => 'lorem',
            'password_confirmation' => 'different',
        ];

        $rule = new Confirmed;
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'password' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new Confirmed($message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new Confirmed();

        $this->assertEquals(__('validation.confirmed'), $rule->message());
    }
}
