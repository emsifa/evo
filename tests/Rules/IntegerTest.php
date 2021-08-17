<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Integer;

class IntegerTest extends TestCase
{
    public function validateProvider()
    {
        return [
            [12345, true],
            ["12345", true],
            [12.345, false],
            ["12.345", false],
            ["lorem ipsum", false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Integer();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Integer(message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Integer();
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.integer'), $accepted->message());
    }
}
