<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Size;

class SizeTest extends TestCase
{
    public function validateProvider()
    {
        return [
            [10, true],
            ["abcdefghij", true],
            [range(1, 10), true],
            [9.9, false],
            [10.1, false],
            ["abcdefghi", false],
            ["abcdefghijk", false],
            [range(1, 9), false],
            [range(1, 11), false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Size(10);
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Size(10, $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Size(10);
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.size.numeric', ['size' => 10]), $accepted->message());
    }
}
