<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Max;

class MaxTest extends TestCase
{
    public function validateProvider()
    {
        return [
            [10, true],
            [9, true],
            ["a string", true],
            [range(1, 10), true],
            [10.1, false],
            [11, false],
            ["a string contains more than 10 chars", false],
            [range(1, 11), false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Max(10);
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Max(10, $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Max(10);
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.max.numeric', ['max' => 10]), $accepted->message());
    }
}
