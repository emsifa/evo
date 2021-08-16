<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\LowerThan;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class LowerThanTest extends TestCase
{
    public function testItShouldBeValidWhenOtherValueLengthIsGreater()
    {
        $data = [
            'a' => 'foobar',
            'b' => 'foo',
        ];

        $rule = new LowerThan('a');
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

        $rule = new LowerThan('a');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'b' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps value must be rule';
        $rule = new LowerThan('a', $message);
        $rule->setData(new ValidationData([
            'a' => 'foobar',
            'b' => 'foo',
        ]));
        $rule->passes('foo', 'bar');

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new LowerThan('a');
        $rule->setData(new ValidationData([
            'a' => 'foobar',
            'b' => 'foo',
        ]));
        $rule->passes('foo', 'bar');

        $this->assertEquals(__('validation.lt.string', ['value' => 'a']), $rule->message());
    }
}
