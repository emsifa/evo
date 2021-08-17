<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Dimensions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class DimensionsTest extends TestCase
{
    public function testItShouldBeValidIfDimensionsIsValid()
    {
        $data = [
            'foo' => UploadedFile::fake()->image('foo.jpg', 100, 500),
        ];

        $validator = Validator::make($data, [
            'foo' => [new Dimensions(100, 200)],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenDimensionsIsValid()
    {
        $data = [
            'foo' => UploadedFile::fake()->image('foo.png', 201, 300),
        ];

        $validator = Validator::make($data, [
            'foo' => [new Dimensions(100, 200)],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new Dimensions(10, 100, message: $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new Dimensions(100, 200);

        $this->assertEquals(__('validation.dimensions'), $rule->message());
    }
}
