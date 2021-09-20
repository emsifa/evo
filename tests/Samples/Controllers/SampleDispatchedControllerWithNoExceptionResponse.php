<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Illuminate\Routing\Controller;
use RuntimeException;

class SampleDispatchedControllerWithNoExceptionResponse extends Controller
{
    public function methodThrowException()
    {
        throw new RuntimeException("Hello there");
    }
}
