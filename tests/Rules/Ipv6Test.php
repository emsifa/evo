<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Ipv6;

class Ipv6Test extends TestCase
{
    public function validateProvider()
    {
        return [
            ["2001:db8:3333:4444:5555:6666:7777:8888", true],
            ["::1", true],
            ["::", true],
            ["2001:db8::", true],
            ["2001:db8:3333:4444:5555:6666:7777:8888:9999", false],
            ["1.2.3.4", false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Ipv6();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Ipv6(message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Ipv6();
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.ipv6'), $accepted->message());
    }
}
