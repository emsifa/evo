<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Mimes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class MimesTest extends TestCase
{
    public function testItShouldBeValidIfMimesAreAllowed()
    {
        $data = [
            'foo' => UploadedFile::fake()->image('foo.jpg'),
        ];

        $validator = Validator::make($data, [
            'foo' => [new Mimes(['jpg'])],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenMimesAreNotAllowed()
    {
        $data = [
            'foo' => UploadedFile::fake()->image('foo.png'),
        ];

        $validator = Validator::make($data, [
            'foo' => [new Mimes(['jpg'])],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new Mimes(['png'], $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new Mimes(['png']);

        $this->assertEquals(__('validation.mimes', ['values' => 'png']), $rule->message());
    }
}
