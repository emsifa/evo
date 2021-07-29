<?php

namespace Emsifa\Evo\Swagger;

use Emsifa\Evo\Swagger\OpenApi\Generator;
use Illuminate\Routing\Controller;

class SwaggerController extends Controller
{
    public function showUi()
    {
        $fallbackTitle = config('app.name') . ' API Documentation';
        $title = config('evo.openapi.info.title', $fallbackTitle);

        return view('evo::swagger-ui', [
            'title' => $title,
        ]);
    }

    public function openApi(Generator $generator)
    {
        return response()->json($generator->getResultArray());
    }
}
