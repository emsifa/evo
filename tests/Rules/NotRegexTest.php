<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\NotRegex;

class NotRegexTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ['123-45', false],
            [' 123-45', true],
            ['123-45 ', true],
            ['0123-456', true],
            ['12a-45', true],
            ['123-4b', true],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new NotRegex("/^\d{3}-\d{2}$/");
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new NotRegex("/^\d{3}-\d{2}$/", message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new NotRegex("/$\d{3}-\d{2}$/");
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.regex'), $accepted->message());
    }
}
