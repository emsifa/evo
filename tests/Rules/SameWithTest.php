<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\SameWith;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class SameWithTest extends TestCase
{
    public function testItShouldBeInvalidWhenOtherValueIsDifferent()
    {
        $data = [
            'a' => 'lorem',
            'b' => 'different',
        ];

        $rule = new SameWith('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testItShouldBeValidWhenOtherValueIsSame()
    {
        $data = [
            'a' => 'lorem',
            'b' => 'lorem',
        ];

        $rule = new SameWith('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new SameWith('a', $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new SameWith('a');

        $this->assertEquals(__('validation.same', ['other' => 'a']), $accepted->message());
    }
}
