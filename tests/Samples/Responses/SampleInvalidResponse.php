<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Contracts\ExceptionResponse;
use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;
use Emsifa\Evo\Types\ArrayOf;
use Exception;
use Illuminate\Validation\ValidationException;

#[ResponseStatus(422)]
class SampleInvalidResponse extends JsonResponse implements ExceptionResponse
{
    public string $message;

    #[ArrayOf('string')]
    public array $errors = [];

    public function forException(Exception $exception): static
    {
        if ($exception instanceof ValidationException) {
            $this->errors = $exception->validator->errors()->all();
        }
        return $this;
    }
}
