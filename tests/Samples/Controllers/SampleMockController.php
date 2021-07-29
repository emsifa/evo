<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Tests\Samples\Responses\SampleErrorResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleNotFoundResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleSuccessResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleUnknownResponse;
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

    public function getMockSuccessResponse(): SampleNotFoundResponse | SampleSuccessResponse
    {
        return new SampleNotFoundResponse();
    }
}
