<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Http\Body;
use Emsifa\Evo\Http\Cookie;
use Emsifa\Evo\Http\File;
use Emsifa\Evo\Http\Header;
use Emsifa\Evo\Http\Param;
use Emsifa\Evo\Http\Query;
use Emsifa\Evo\Route\Post;
use Emsifa\Evo\Route\RoutePrefix;
use Emsifa\Evo\Swagger\OpenApi\Description;
use Emsifa\Evo\Swagger\OpenApi\Example;
use Emsifa\Evo\Swagger\OpenApi\Summary;
use Emsifa\Evo\Tests\Samples\DTO\SwaggerPostStuffDTO;
use Emsifa\Evo\Tests\Samples\Responses\PostStuffResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleErrorResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleNotFoundResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleSuccessResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleUnknownResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;

class SampleMockController extends Controller
{
    public function onlyOneResponse(): SampleUnknownResponse
    {
        return new SampleUnknownResponse();
    }

    public function unionResponse(): SampleNotFoundResponse | SampleSuccessResponse | SampleErrorResponse
    {
        return new SampleNotFoundResponse();
    }

    public function unionResponseWithNoSuccess(): SampleUnknownResponse | SampleErrorResponse
    {
        return new SampleUnknownResponse();
    }
}
