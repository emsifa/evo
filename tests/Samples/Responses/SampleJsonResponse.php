<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;

class SampleJsonResponse extends JsonResponse
{
    public int $id;
    public string $title;
}
