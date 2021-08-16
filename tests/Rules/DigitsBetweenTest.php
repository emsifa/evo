<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\DigitsBetween;

class DigitsBetweenTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ['12345', true],
            ['123456', true],
            ['1234', false],
            ['123456789', false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new DigitsBetween(5, 8);
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new DigitsBetween(10, 8, $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new DigitsBetween(5, 8);
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.digits_between', ['min' => 5, 'max' => 8]), $accepted->message());
    }
}
