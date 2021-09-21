<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Tests\Samples\Dto\SampleDtoWithFile;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class DtoTest extends TestCase
{
    public function testFromRequest()
    {
        $request = new Request(
            query: [
                'int' => "12",
                'string' => "text",
                'bool' => "true",
            ],
            files: [
                'file' => UploadedFile::fake()->image('lorem.jpg'),
            ],
        );

        $result = SampleDtoWithFile::fromRequest($request);

        $this->assertTrue($result instanceof SampleDtoWithFile);
        $this->assertEquals(12, $result->int);
        $this->assertEquals("text", $result->string);
        $this->assertEquals(true, $result->bool);
        $this->assertEquals("lorem.jpg", $result->file->getClientOriginalName());
    }
}
