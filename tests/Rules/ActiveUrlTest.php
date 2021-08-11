<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\ActiveUrl;

class ActiveUrlTest extends TestCase
{

    public function validateProvider()
    {
        return [
            ['https://www.google.com', true],
            ['https://www.facebook.com', true],
            ['http://just.a.random.url.co', true],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new ActiveUrl();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'opps value must be accepted';
        $accepted = new ActiveUrl($message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new ActiveUrl();

        $this->assertEquals(__('validation.active_url'), $accepted->message());
    }
}
