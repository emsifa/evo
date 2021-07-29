<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Http\Response\Mock;
use Emsifa\Evo\Tests\Samples\Responses\SampleSuccessResponse;
use Illuminate\Routing\Controller;

class SampleDispatchedController extends Controller
{
    #[Mock]
    public function methodWithMock(): SampleSuccessResponse
    {
    }

    #[Mock(optional: true)]
    public function methodWithOptionalMock(): SampleSuccessResponse
    {
        return SampleSuccessResponse::fromArray([
            'id' => 456,
            'name' => "Nikola Tesla",
        ]);
    }
}
