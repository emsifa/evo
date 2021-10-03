<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Http\Response\UseErrorResponse;
use Emsifa\Evo\Tests\Samples\Exceptions\CustomExceptionWithResponseStatus;
use Emsifa\Evo\Tests\Samples\Responses\ErrorResponseNoStatus;
use Emsifa\Evo\Tests\Samples\Responses\SampleErrorResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use RuntimeException;

#[UseErrorResponse(SampleErrorResponse::class)]
#[UseErrorResponse(ErrorResponseNoStatus::class, [InvalidArgumentException::class])]
class ControllerDispatcherTestController extends Controller
{
    public function throwCustomExceptionWithResponseStatus()
    {
        throw new CustomExceptionWithResponseStatus("There is something wrong.");
    }

    public function throwExceptionWithNoResponseStatus()
    {
        throw new RuntimeException("I am runtime exception.");
    }

    public function throwExceptionWithResponseNoResponseStatus()
    {
        throw new InvalidArgumentException("I am invalid argument exception.");
    }
}
