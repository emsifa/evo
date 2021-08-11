<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Alpha;

class AlphaTest extends TestCase
{

    public function validateProvider()
    {
        return [
            ['abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', true],
            ['abc123', false],
            ['abc ', false],
            [' abc', false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Alpha();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Alpha($message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Alpha();

        $this->assertEquals(__('validation.alpha'), $accepted->message());
    }
}
