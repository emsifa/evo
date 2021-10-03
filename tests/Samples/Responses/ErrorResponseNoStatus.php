<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;

class ErrorResponseNoStatus extends JsonResponse
{
    public string $message;
}
