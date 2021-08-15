<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Different;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class DifferentTest extends TestCase
{
    public function testItShouldBeInvalidWhenOtherValueIsSame()
    {
        $data = [
            'a' => 'lorem',
            'b' => 'lorem',
        ];

        $rule = new Different('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testItShouldBeValidWhenOtherValueIsDifferent()
    {
        $data = [
            'a' => 'lorem',
            'b' => 'different',
        ];

        $rule = new Different('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Different('a', $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Different('a');

        $this->assertEquals(__('validation.different', ['other' => 'a']), $accepted->message());
    }
}
