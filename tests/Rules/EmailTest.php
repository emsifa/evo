<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Email;

class EmailTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ["a.valid@mail.domain", true],
            ["also.valid.mail@sub.dom.ain", true],
            ["some1@sub.dom.ain", true],
            ["invalid@mail@domain", false],
            ["@invalid.mail", false],
            ["also.invalid.mail", false],
            ["absolutely not an email", false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Email();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Email(10, message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Email();
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.email'), $accepted->message());
    }
}
