<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Min;

class MinTest extends TestCase
{
    public function validateProvider()
    {
        return [
            [10, true],
            [11, true],
            [100, true],
            [9.9, false],
            [9, false],
            [0, false],
            ["string", false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Min(10);
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Min(10, $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Min(10);
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.min.numeric', ['min' => 10]), $accepted->message());
    }
}
