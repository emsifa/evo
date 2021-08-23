<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\ValidationData;

class ValidationDataTest extends TestCase
{
    public function testValidationData()
    {
        $data = new ValidationData([
            "foo" => "bar",
        ]);

        $this->assertEquals($data["foo"], "bar");

        $data["foo"] = "baz";
        $this->assertEquals($data["foo"], "baz");

        unset($data["foo"]);
        $this->assertEquals([], $data->toArray());
    }
}
