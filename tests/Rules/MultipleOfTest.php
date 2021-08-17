<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\MultipleOf;

class MultipleOfTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ["6", true],
            ["9", true],
            ["12", true],
            ["15", true],
            ["5", false],
            ["7", false],
            ["10", false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new MultipleOf(3);
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new MultipleOf(3, message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new MultipleOf(3);
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.multiple_of', ['value' => 3]), $accepted->message());
    }
}
