<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Timezone;

class TimezoneTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ["UTC", true],
            ["Asia/Jakarta", true],
            ["Lorem/Ipsum", false],
            ["Asia/London", false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Timezone();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Timezone(message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Timezone();
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.timezone'), $accepted->message());
    }
}
