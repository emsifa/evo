<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;

#[ResponseStatus(404)]
class SampleNotFoundResponse extends JsonResponse
{
    public string $message;
}
