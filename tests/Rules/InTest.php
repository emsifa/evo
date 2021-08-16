<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\In;
use Illuminate\Support\Facades\Validator;

class InTest extends TestCase
{
    public function testItShouldBeValidWhenValueWhitelisted()
    {
        $data = [
            'x' => 'lorem',
        ];

        $validator = Validator::make($data, [
            'x' => [new In(['lorem', 'ipsum', 'dolor'])],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenValueIsNotWhitelisted()
    {
        $data = [
            'x' => 'lorem',
        ];

        $validator = Validator::make($data, [
            'x' => [new In(['foo', 'bar', 'baz'])],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new In([1, 2, 3], $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new In([1, 2, 3]);

        $this->assertEquals(__('validation.in'), $rule->message());
    }
}
