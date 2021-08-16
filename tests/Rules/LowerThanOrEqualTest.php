<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\LowerThanOrEqual;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class LowerThanOrEqualTest extends TestCase
{
    public function testItShouldBeValidWhenOtherValueLengthIsEquals()
    {
        $data = [
            'a' => 'foobar',
            'b' => 'foobar',
        ];

        $rule = new LowerThanOrEqual('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeValidWhenOtherValueLengthIsGreater()
    {
        $data = [
            'a' => 'foobar',
            'b' => 'foo',
        ];

        $rule = new LowerThanOrEqual('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenOtherValueLengthIsLower()
    {
        $data = [
            'a' => 'foo',
            'b' => 'foobar',
        ];

        $rule = new LowerThanOrEqual('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps value must be rule';
        $rule = new LowerThanOrEqual('a', $message);
        $rule->setData(new ValidationData([
            'a' => 'foobar',
            'b' => 'foo',
        ]));
        $rule->passes('foo', 'bar');

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new LowerThanOrEqual('a');
        $rule->setData(new ValidationData([
            'a' => 'foobar',
            'b' => 'foo',
        ]));
        $rule->passes('foo', 'bar');

        $this->assertEquals(__('validation.lte.string', ['value' => 'a']), $rule->message());
    }
}
