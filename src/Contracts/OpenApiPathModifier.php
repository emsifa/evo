<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\Schemas\PathSchema;

interface OpenApiPathModifier
{
    public function modifyOpenApiPath(PathSchema $path);
}
