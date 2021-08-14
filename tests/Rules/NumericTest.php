<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Numeric;

class NumericTest extends TestCase
{
    public function validateProvider()
    {
        return [
            [12345, true],
            [123.45, true],
            ["12345", true],
            ["123.45", true],
            ["123x", false],
            ["x123", false],
            ["", false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Numeric();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Numeric(message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Numeric();
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.numeric'), $accepted->message());
    }
}
