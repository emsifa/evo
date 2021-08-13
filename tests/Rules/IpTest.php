<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Ip;

class IpTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ["255.255.255.255", true],
            ["0.0.0.0", true],
            ["2001:db8:3333:4444:5555:6666:7777:8888", true],
            ["::1", true],
            ["::", true],
            ["2001:db8::", true],
            ["2001:db8:3333:4444:5555:6666:7777:8888:9999", false],
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
        $accepted = new Ip();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Ip(message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Ip();
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.ip'), $accepted->message());
    }
}
