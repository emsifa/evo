<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\In;
use Emsifa\Evo\Rules\InArray;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class InArrayTest extends TestCase
{
    public function testItShouldBeValidWhenValueExists()
    {
        $data = [
            'x' => 'lorem',
            'arr' => ['lorem', 'ipsum', 'dolor']
        ];

        $rule = new InArray('arr.*');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'x' => [$rule],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenValueIsNotExists()
    {
        $data = [
            'x' => 'lorem',
            'arr' => ['foo', 'bar', 'baz'],
        ];

        $rule = new InArray('arr.*');
        $rule->setData(new ValidationData($data));

        $validator = Validator::make($data, [
            'x' => [$rule],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new InArray('foo', $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new InArray('foo');

        $this->assertEquals(__('validation.in_array', ['other' => 'foo']), $rule->message());
    }
}
