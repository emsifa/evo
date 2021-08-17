<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\MimeTypes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class MimeTypesTest extends TestCase
{
    public function testItShouldBeValidIfMimeTypesAreAllowed()
    {
        $data = [
            'foo' => UploadedFile::fake()->image('foo.jpg'),
        ];

        $validator = Validator::make($data, [
            'foo' => [new MimeTypes(['image/jpeg'])],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenMimeTypesAreNotAllowed()
    {
        $data = [
            'foo' => UploadedFile::fake()->image('foo.png'),
        ];

        $validator = Validator::make($data, [
            'foo' => [new MimeTypes(['image/jpeg'])],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new MimeTypes(['image/png'], $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new MimeTypes(['image/png']);

        $this->assertEquals(__('validation.mimetypes', ['values' => 'image/png']), $rule->message());
    }
}
