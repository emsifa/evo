<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\StartsWith;

class StartsWithTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ['foobar', true],
            ['foo-123', true],
            ['afoobar', false],
            [' foobar', false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new StartsWith('foo');
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new StartsWith('foo', $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new StartsWith('foo');

        $this->assertEquals(__('validation.starts_with', ['values' => 'foo']), $accepted->message());
    }
}
