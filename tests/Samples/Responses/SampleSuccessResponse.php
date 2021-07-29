<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;

#[ResponseStatus(201)]
class SampleSuccessResponse extends JsonResponse
{
    public int $id;
    public string $name;
}
