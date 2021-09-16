<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Tests\Samples\SampleBodySchema;
use Illuminate\Routing\Controller;

class BodyTestController extends Controller
{
    public function methodWithMixedParam($data)
    {
    }

    public function methodWithNonDtoParam(SampleBodySchema $data)
    {
    }
}
