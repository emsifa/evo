<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Present;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class PresentTest extends TestCase
{
    public function testItShouldBeValidWhenKeyIsPresentEvenItsValueIsEmpty()
    {
        $data = [
            'foo' => '',
        ];

        $rule = new Present();
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenKeyIsNotPresent()
    {
        $data = [];

        $rule = new Present();
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'foo' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new Present($message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new Present();

        $this->assertEquals(__('validation.present'), $rule->message());
    }
}
