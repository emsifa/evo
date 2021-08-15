<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\DifferentWith;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class DifferentWithTest extends TestCase
{
    public function testItShouldBeInvalidWhenOtherValueIsSame()
    {
        $data = [
            'a' => 'lorem',
            'b' => 'lorem',
        ];

        $rule = new DifferentWith('a');
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

        $rule = new DifferentWith('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new DifferentWith('a', $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new DifferentWith('a');

        $this->assertEquals(__('validation.different', ['other' => 'a']), $accepted->message());
    }
}
