<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\AlphaDash;

class AlphaDashTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ['abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', true],
            ['abd-def', true],
            ['abc-123', true],
            ['abc_123', true],
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
        $accepted = new AlphaDash();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new AlphaDash($message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new AlphaDash();

        $this->assertEquals(__('validation.alpha_dash'), $accepted->message());
    }
}
