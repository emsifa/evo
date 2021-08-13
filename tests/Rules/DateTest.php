<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Date;

class DateTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ['2010-01-02', true],
            ['2010-01-02 03:04:05', true],
            ['01/02/2004/10', false],
            ['lorem ipsum', false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Date();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Date($message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Date();

        $this->assertEquals(__('validation.date'), $accepted->message());
    }
}
