<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\GreaterThan;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class GreaterThanTest extends TestCase
{
    public function testItShouldBeValidWhenOtherValueLengthIsLower()
    {
        $data = [
            'a' => 'foo',
            'b' => 'foobar',
        ];

        $rule = new GreaterThan('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenOtherValueLengthIsGreater()
    {
        $data = [
            'a' => 'foobar',
            'b' => 'foo',
        ];

        $rule = new GreaterThan('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps value must be rule';
        $rule = new GreaterThan('a', $message);
        $rule->setData(new ValidationData([
            'a' => 'foobar',
            'b' => 'foo',
        ]));
        $rule->passes('foo', 'bar');

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new GreaterThan('a');
        $rule->setData(new ValidationData([
            'a' => 'foobar',
            'b' => 'foo',
        ]));
        $rule->passes('foo', 'bar');

        $this->assertEquals(__('validation.gt.string', ['value' => 'a']), $rule->message());
    }
}
