<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\DateBeforeOrEqual;

class DateBeforeOrEqualTest extends TestCase
{
    public function validateProvider()
    {
        return [
            [date_create('today')->modify('-1 day')->format('Y-m-d 00:00:00'), true],
            [date_create('today')->format('Y-m-d 00:00:00'), true],
            [date_create('today')->modify('+1 day')->format('Y-m-d 00:00:00'), false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new DateBeforeOrEqual('today');
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new DateBeforeOrEqual('today', $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new DateBeforeOrEqual('today');

        $this->assertEquals(__('validation.before_or_equal', ['date' => date_create('today')->format('Y-m-d H:i:s')]), $accepted->message());
    }
}
