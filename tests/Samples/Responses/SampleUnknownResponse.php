<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;

class SampleUnknownResponse extends JsonResponse
{
    public int $id;
    public string $name;
}
