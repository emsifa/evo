<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Boolean;

class BooleanTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ['1', true],
            [1, true],
            [true, true],
            ['0', true],
            [0, true],
            [false, true],

            ['random value', false],
            [123, false],
            [[1,2,3], false],
            [new \stdClass, false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Boolean();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'opps value must be accepted';
        $accepted = new Boolean($message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Boolean();

        $this->assertEquals(__('validation.boolean'), $accepted->message());
    }
}
