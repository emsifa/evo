<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\DateFormat;

class DateFormatTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ['2010-01-02', true],
            ['01/02/2003', false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new DateFormat('Y-m-d');
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new DateFormat('Y-m-d', $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new DateFormat('Y-m-d');

        $this->assertEquals(__('validation.date_format', ['format' => 'Y-m-d']), $accepted->message());
    }
}
