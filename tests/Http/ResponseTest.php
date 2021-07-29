<?php

namespace Emsifa\Evo\Http;

use Emsifa\Evo\Tests\Samples\Http\CreateStuffResponse;
use Emsifa\Evo\Tests\Samples\Http\CreateUserResponse;
use Emsifa\Evo\Tests\Samples\Http\SampleViewResponse;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ResponseTest extends TestCase
{
    public function testTemplatedJsonResponse()
    {
        $request = new Request();
        $response = CreateUserResponse::fromArray([
            'id' => "1",
            'name' => "John Doe",
            'email' => "johndoe@mail.com",
            'createdAt' => "2015-12-15",
        ])
        ->withStatus(CreateUserResponse::STATUS_OK)
        ->toResponse($request);

        $expected = json_encode([
            'status' => CreateUserResponse::STATUS_OK,
            'data' => [
                'id' => 1,
                'name' => "John Doe",
                'email' => "johndoe@mail.com",
                'createdAt' => "2015-12-15",
            ],
        ]);

        $this->assertEquals($expected, $response->getContent());
    }

    public function testTemplatedJsonResponseWithProperties()
    {
        $request = new Request();
        $response = CreateStuffResponse::fromArray([
            'id' => "1",
            'stuff' => "a stuff",
        ])
        ->toResponse($request);

        $expected = json_encode([
            'status' => 201,
            'data' => [
                'id' => 1,
                'stuff' => "a stuff",
            ],
        ]);

        $this->assertEquals($expected, $response->getContent());
    }

    public function testViewResponse()
    {
        View::getFinder()->setPaths([__DIR__.'/views']);

        $request = new Request();
        $response = SampleViewResponse::fromArray([
            'id' => "1",
            'name' => "John Doe",
            'email' => "johndoe@mail.com",
            'createdAt' => "2015-12-15",
        ])
        ->toResponse($request);

        $expected = implode(", ", [
            "ID: 1",
            "Name: John Doe",
            "Email: johndoe@mail.com",
            "Created At: 2015-12-15",
        ]);

        $this->assertStringContainsString($expected, $response->getContent());
    }
}
