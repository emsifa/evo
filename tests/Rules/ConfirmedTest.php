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

        $confirmedRule = new Confirmed;
        $confirmedRule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'password' => [$confirmedRule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps value must be accepted';
        $accepted = new Confirmed($message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Confirmed();

        $this->assertEquals(__('validation.confirmed'), $accepted->message());
    }
}
