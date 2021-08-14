<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Json;

class JsonTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ['{"foo": "bar"}', true],
            ['{foo: "bar"}', false],
            ['{"foo"}', false],
            ['{"foo": "bar",}', false],
            ['[]', true],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Json();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Json(message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Json();
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.json'), $accepted->message());
    }
}
