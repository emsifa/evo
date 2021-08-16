<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\NotIn;
use Emsifa\Evo\ValidationData;
use Illuminate\Support\Facades\Validator;

class NotInTest extends TestCase
{
    public function testItShouldBeInvalidWhenValueWhitelisted()
    {
        $data = [
            'x' => 'lorem',
        ];

        $validator = Validator::make($data, [
            'x' => [new NotIn(['lorem', 'ipsum', 'dolor'])],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testItShouldBeValidWhenValueIsNotWhitelisted()
    {
        $data = [
            'x' => 'lorem',
        ];

        $validator = Validator::make($data, [
            'x' => [new NotIn(['foo', 'bar', 'baz'])],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new NotIn([1, 2, 3], $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new NotIn([1, 2, 3]);

        $this->assertEquals(__('validation.not_in'), $rule->message());
    }
}
