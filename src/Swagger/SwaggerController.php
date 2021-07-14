<?php

namespace Emsifa\Evo\Swagger;

use Illuminate\Routing\Controller;

class SwaggerController extends Controller
{
    public function showUi()
    {
        return view('evo::swagger-ui');
    }

    public function openApi()
    {
    }
}
