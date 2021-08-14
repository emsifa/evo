<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Uuid;

class UuidTest extends TestCase
{
    public function validateProvider()
    {
        return [
            ["c37860c0-fc95-11eb-9a03-0242ac130003", true],
            ["55a13c5e-c406-4516-84c8-4529e2e57b76", true],
            ["c37860c0-fc95-11eb-9a03", false],
            ["c37860c01-fc95-11eb-9a03-0242ac130003", false],
            ["c37860c0-fc951-11eb-9a03-0242ac130003", false],
            ["c37860c0-fc95-11eb1-9a03-0242ac130003", false],
            ["c37860c0-fc95-11eb-9a034-0242ac130003", false],
            ["x37860c0-fc95-11eb-9a03-0242ac130003", false],
        ];
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate($value, $expected)
    {
        $accepted = new Uuid();
        $result = $accepted->passes('foo', $value);

        $this->assertEquals($expected, $result);
    }

    public function testOverrideMessage()
    {
        $message = 'oppss invalid value';
        $accepted = new Uuid(message: $message);

        $this->assertEquals($message, $accepted->message());
    }

    public function testFallbackMessage()
    {
        $accepted = new Uuid();
        $accepted->passes('foo', 10);

        $this->assertEquals(__('validation.uuid'), $accepted->message());
    }
}
