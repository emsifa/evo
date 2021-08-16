<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\GreaterThanOrEqual;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class GreaterThanOrEqualTest extends TestCase
{
    public function testItShouldBeValidWhenOtherValueLengthIsEquals()
    {
        $data = [
            'a' => 'foobar',
            'b' => 'foobar',
        ];

        $rule = new GreaterThanOrEqual('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeValidWhenOtherValueLengthIsLower()
    {
        $data = [
            'a' => 'foo',
            'b' => 'foobar',
        ];

        $rule = new GreaterThanOrEqual('a');
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

        $rule = new GreaterThanOrEqual('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps value must be rule';
        $rule = new GreaterThanOrEqual('a', $message);
        $rule->setData(new ValidationData([
            'a' => 'foobar',
            'b' => 'foo',
        ]));
        $rule->passes('foo', 'bar');

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new GreaterThanOrEqual('a');
        $rule->setData(new ValidationData([
            'a' => 'foobar',
            'b' => 'foo',
        ]));
        $rule->passes('foo', 'bar');

        $this->assertEquals(__('validation.gte.string', ['value' => 'a']), $rule->message());
    }
}
