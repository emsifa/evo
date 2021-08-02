<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;
use Emsifa\Evo\Types\ArrayOf;

#[ResponseStatus(500)]
class SampleInvalidResponse extends JsonResponse
{
    public string $message;

    #[ArrayOf('string')]
    public array $errors;
}
