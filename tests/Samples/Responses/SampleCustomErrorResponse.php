<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Contracts\ExceptionResponse;
use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;
use Exception;

#[ResponseStatus(500)]
class SampleCustomErrorResponse extends JsonResponse implements ExceptionResponse
{
    public string $code;
    public string $message;

    public function forException(Exception $exception): static
    {
        $this->code = "E102";
        $this->message = $exception->getMessage();

        return $this;
    }
}
