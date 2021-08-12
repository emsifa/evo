<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\ArrayWith;

class ArrayWithTest extends TestCase
{
    public function validateProvider()
    {
        return [
            [['x' => 123], true],
            [['y' => 123], false],
            [['x' => 456, 'y' => 123], false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new ArrayWith(['x']);
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new ArrayWith(['x'], $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new ArrayWith(['foo']);

        $this->assertEquals(__('validation.array'), $accepted->message());
    }
}
