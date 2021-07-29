<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\DTO\UseFaker;
use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;

#[ResponseStatus(201)]
class SampleSuccessResponse extends JsonResponse
{
    #[UseFaker("randomElement", [1, 2, 3])]
    public int $id;

    #[UseFaker("randomElement", ["John Doe", "Jane Doe"])]
    public string $name;
}
