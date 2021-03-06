<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;

#[ResponseStatus(510)]
class SampleErrorResponse extends JsonResponse
{
    public string $message;
}
