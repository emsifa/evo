<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Digits;

class DigitsTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ['12345', true],
            ['123456', false],
            ['1234', false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Digits(5);
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Digits(10, $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Digits(5);
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.digits', ['digits' => 5]), $accepted->message());
    }
}
