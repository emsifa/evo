<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;

class SampleUnknownResponse extends JsonResponse
{
    public int $id;
    public string $name;
}
