<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Http\Query;
use Emsifa\Evo\Http\Response\Mock;
use Emsifa\Evo\Http\Response\UseErrorResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleCustomErrorResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleErrorResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleInvalidResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleSuccessResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

#[UseErrorResponse(SampleErrorResponse::class)]
#[UseErrorResponse(SampleErrorResponse::class, [InvalidArgumentException::class])]
#[UseErrorResponse(SampleInvalidResponse::class, [ValidationException::class])]
class SampleDispatchedController extends Controller
{
    #[Mock]
    public function methodWithMock(): SampleSuccessResponse
    {
        return SampleSuccessResponse::fromArray([
            'id' => 789,
            'name' => "John Doe",
        ]);
    }

    #[Mock(optional: true)]
    public function methodWithOptionalMock(): SampleSuccessResponse
    {
        return SampleSuccessResponse::fromArray([
            'id' => 456,
            'name' => "Nikola Tesla",
        ]);
    }

    #[UseErrorResponse(SampleCustomErrorResponse::class, [InvalidArgumentException::class])]
    public function methodWithSpecificErrorResponse()
    {
        throw new InvalidArgumentException("Whops! something went wrong");
    }

    public function methodThrownValidationException(#[Query] int $number)
    {
    }
}
