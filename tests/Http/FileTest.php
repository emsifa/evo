<?php

namespace Emsifa\Evo\Tests\Http;

use Emsifa\Evo\Http\File;
use Emsifa\Evo\Tests\Samples\Casters\UploadedFilePathCaster;
use Emsifa\Evo\Tests\Samples\UploadedFile as SampleUploadedFile;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use ReflectionNamedType;
use ReflectionParameter;
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

    private function getMockReflectionParam($name, string $typeName = '', $allowsNull = false)
    {
        if ($typeName) {
            $type = $this->createStub(ReflectionNamedType::class);
            $type->method('getName')->willReturn($typeName);
            $type->method('allowsNull')->willReturn($allowsNull);
        }

        $reflection = $this->createStub(ReflectionParameter::class);
        $reflection->method('getName')->willReturn($name);
        $reflection->method('getType')->willReturn($typeName ? $type : null);

        return $reflection;
    }
}
