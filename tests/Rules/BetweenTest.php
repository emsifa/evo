<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Between;

class BetweenTest extends TestCase
{
    public function validateProvider()
    {
        return [
            [1, true],
            [5, true],
            [10, true],
            [11, false],
            [10.1, false],
            [0, false],
            [0.9, false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Between(1, 10);
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Between(1, 10, $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Between(1, 10);
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.between.numeric', ['min' => 1, 'max' => 10]), $accepted->message());
    }
}
