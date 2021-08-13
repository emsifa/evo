<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Ipv4;

class Ipv4Test extends TestCase
{
    public function validateProvider()
    {
        return [
            ["255.255.255.255", true],
            ["0.0.0.0", true],
            ["0.0.0.256", false],
            ["-1.0.0.0", false],
            ["1.2.3.4.5", false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Ipv4();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Ipv4(message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Ipv4();
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.ipv4'), $accepted->message());
    }
}
