<?php

namespace Emsifa\Evo\Tests\Http;

use Emsifa\Evo\Http\File;
use Emsifa\Evo\Tests\Samples\Casters\UploadedFilePathCaster;
use Emsifa\Evo\Tests\Samples\UploadedFile as SampleUploadedFile;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;

class FileTest extends TestCase
{
    public function testGetValueFromFile()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('image', UploadedFile::class);
        $request = $this->makeRequestWithRouteFiles(["image" => __DIR__."/files/dot.png"]);
        $param = new File();

        /**
         * @var UploadedFile
         */
        $result = $param->getRequestValue($request, $reflection);

        $this->assertInstanceOf(UploadedFile::class, $result);
        $this->assertEquals($result->getClientOriginalName(), 'dot.png');
    }

    public function testGetValueFromFileWithDifferentKeyName()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('foo', 'string');
        $request = $this->makeRequestWithRouteFiles(["image" => __DIR__."/files/dot.png"]);
        $param = new File('image');
        $result = $param->getRequestValue($request, $reflection);

        $this->assertInstanceOf(UploadedFile::class, $result);
        $this->assertEquals($result->getClientOriginalName(), 'dot.png');
    }

    public function testGetValueFromFileWithCaster()
    {
        $path = __DIR__."/files/dot.png";
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('image', 'string');
        $request = $this->makeRequestWithRouteFiles(["image" => $path]);
        $param = new File('image', caster: UploadedFilePathCaster::class);
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals($path, $result);
    }

    public function testValidationSucceed()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('image', UploadedFile::class);
        $request = $this->makeRequestWithRouteFiles(["image" => __DIR__."/files/dot.png"]);
        $param = new File('image', rules: 'file|mimes:png');

        $this->assertNull($param->validateRequest($request, $reflection));
    }

    public function testValidationError()
    {
        $this->expectException(ValidationException::class);

        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('image', UploadedFile::class);
        $request = $this->makeRequestWithRouteFiles(["image" => __DIR__."/files/dot.png"]);
        $param = new File('image', rules: 'file|mimes:pdf');

        $param->validateRequest($request, $reflection);
    }

    public function testGetAssignedDefaultValue()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', UploadedFile::class, null, null);
        $request = $this->makeRequestWithRouteFiles([]);
        $param = new File('id');
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals(null, $result);
    }

    private function makeRequestWithRouteFiles(array $files): Request
    {
        $request = new Request();
        $uploadedFiles = [];
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        foreach ($files as $key => $filepath) {
            $uploadedFiles[$key] = new SampleUploadedFile(
                $filepath,
                pathinfo($filepath, PATHINFO_BASENAME),
                $mimeTypeGuesser->guessMimeType($filepath),
            );
        }
        $request->files->add($uploadedFiles);

        return $request;
    }
}
