<?php

namespace Emsifa\Evo\Tests\Http;

use Emsifa\Evo\Http\Body;
use Emsifa\Evo\Tests\Samples\Controllers\BodyTestController;
use Emsifa\Evo\Tests\Samples\MockPresenceVerifier;
use Emsifa\Evo\Tests\Samples\SampleBodySchema;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use ReflectionClass;
use ReflectionMethod;

class BodyTest extends TestCase
{
    public function testGetDescription()
    {
        $body = new Body(description: "Lorem Ipsum");
        $this->assertEquals("Lorem Ipsum", $body->getDescription());
    }

    public function testGetRequestValueFromMixedTypeShouldReturnAllData()
    {
        $body = new Body();
        $data = ['a' => 1, 'b' => 2, 'c' => 'xyz'];
        $request = new Request(request: $data, server: ['REQUEST_METHOD' => 'POST']);
        $parameter = (new ReflectionClass(BodyTestController::class))
            ->getMethod("methodWithMixedParam")
            ->getParameters()[0];
        $value = $body->getRequestValue($request, $parameter);

        $this->assertEquals($data, $value);
    }

    public function testGetRequestValueFromNonDtoTypeShouldAlsoFillObject()
    {
        $body = new Body();
        $data = ['name' => 'Lorem', 'email' => 'lorem@ipsum.com'];
        $request = new Request(request: $data, server: ['REQUEST_METHOD' => 'POST']);
        $parameter = (new ReflectionClass(BodyTestController::class))
            ->getMethod("methodWithNonDtoParam")
            ->getParameters()[0];
        $value = $body->getRequestValue($request, $parameter);

        $this->assertInstanceOf(SampleBodySchema::class, $value);
        $this->assertEquals("Lorem", $value->name);
        $this->assertEquals("lorem@ipsum.com", $value->email);
    }

    public function testGetMergedInputsAndFiles()
    {
        $body = new Body();
        $data = ['name' => 'Lorem', 'email' => 'lorem@ipsum.com'];
        $request = new Request(
            request: $data,
            files: [
                'file' => UploadedFile::fake()->image('test.jpg'),
            ],
            server: ['REQUEST_METHOD' => 'POST']
        );

        $getMergedInputsAndFiles = new ReflectionMethod($body, "getMergedInputsAndFiles");
        $getMergedInputsAndFiles->setAccessible(true);

        $result = $getMergedInputsAndFiles->invoke($body, $request);

        $this->assertEquals("Lorem", $result['name']);
        $this->assertEquals("lorem@ipsum.com", $result['email']);
        $this->assertEquals("test.jpg", $result["file"]->getClientOriginalName());
    }

    public function testValidateRequestFromMixedTypeShouldNotValidateAnything()
    {
        $body = new Body();
        $data = ['a' => 1, 'b' => 2, 'c' => 'xyz'];
        $request = new Request(request: $data, server: ['REQUEST_METHOD' => 'POST']);
        $parameter = (new ReflectionClass(BodyTestController::class))
            ->getMethod("methodWithMixedParam")
            ->getParameters()[0];

        $this->assertNull($body->validateRequest($request, $parameter));
    }

    public function testValidateRequestToDtoThatHasPresenceVerifierInItsChildObjects()
    {
        $body = new Body();
        $body->setPresenceVerifier(new MockPresenceVerifier(collect([
            'users' => collect([
                ['id' => 1, 'name' => 'Foo'],
                ['id' => 2, 'name' => 'Bar'],
                ['id' => 3, 'name' => 'Baz'],
            ]),
        ])));

        $data = [
            'user' => [
                'user_id' => 1,
            ],
            'users' => [
                ['user_id' => 2],
                ['user_id' => 3],
            ],
        ];
        $request = new Request(request: $data, server: ['REQUEST_METHOD' => 'POST']);
        $parameter = (new ReflectionClass(BodyTestController::class))
            ->getMethod("methodWithPresenceVerifierInChildObjects")
            ->getParameters()[0];

        $this->assertNull($body->validateRequest($request, $parameter));
    }
}
