<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Dto\UseFaker;
use Emsifa\Evo\Http\Response\JsonResponse;

class SampleWrongMockResponse extends JsonResponse
{
    #[UseFaker("Hulululu")]
    public string $str;
}
