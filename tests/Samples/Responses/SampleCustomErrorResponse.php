<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;

#[ResponseStatus(500)]
class SampleCustomErrorResponse extends JsonResponse
{
    public string $code;
    public string $message;
}
