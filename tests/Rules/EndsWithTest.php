<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\EndsWith;

class EndsWithTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ['barfoo', true],
            ['123foo', true],
            ['barfooo', false],
            ['barfoo ', false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new EndsWith('foo');
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new EndsWith('foo', $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new EndsWith('foo');

        $this->assertEquals(__('validation.ends_with', ['values' => 'foo']), $accepted->message());
    }
}
