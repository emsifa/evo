<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Url;

class UrlTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ["https://www.google.com", true],
            ["https://www.google.com/search?q=lorem%20ipsum", true],
            ["https://site.com/foo/bar?x=1&y=2#section", true],
            ["site.com", false],
            ["lorem ipsum", false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Url();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Url(message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Url();
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.url'), $accepted->message());
    }
}
