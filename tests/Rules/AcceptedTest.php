<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Accepted;

class AcceptedTest extends TestCase
{

    public function validateProvider()
    {
        return [
            ['yes', true],
            ['on', true],
            ['1', true],
            [1, true],
            ['true', true],
            [true, true],

            ['no', false],
            ['off', false],
            ['0', false],
            [0, false],
            ['false', false],
            [false, false],

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
        $accepted = new Accepted();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'opps value must be accepted';
        $accepted = new Accepted($message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Accepted();

        $this->assertEquals(__('validation.accepted'), $accepted->message());
    }
}
