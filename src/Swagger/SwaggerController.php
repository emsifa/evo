<?php

namespace Emsifa\Evo\Swagger;

use Emsifa\Evo\Swagger\OpenApi\Generator;
use Illuminate\Routing\Controller;

class SwaggerController extends Controller
{
    public function showUi()
    {
        return view('evo::swagger-ui');
    }

    public function openApi(Generator $generator)
    {
        return response()->json($generator->getResultArray());
    }
}
