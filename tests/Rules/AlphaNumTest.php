<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\AlphaNum;

class AlphaNumTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ['abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', true],
            ['123456', true],
            ['abc123', true],
            ['abc-123', false],
            ['abc_123', false],
            ['abc 123', false],
            ['abc ', false],
            [' 123', false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new AlphaNum();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new AlphaNum($message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new AlphaNum();

        $this->assertEquals(__('validation.alpha_num'), $accepted->message());
    }
}
