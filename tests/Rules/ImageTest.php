<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Image;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class ImageTest extends TestCase
{
    public function testItShouldBeValidIfFileIsNotImage()
    {
        $data = [
            'foo' => UploadedFile::fake()->image('foo.jpg'),
        ];

        $validator = Validator::make($data, [
            'foo' => [new Image()],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidWhenImageAreNotAllowed()
    {
        $data = [
            'foo' => new File(__FILE__),
        ];

        $validator = Validator::make($data, [
            'foo' => [new Image()],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new Image($message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new Image();

        $this->assertEquals(__('validation.image'), $rule->message());
    }
}
