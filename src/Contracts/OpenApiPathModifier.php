<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\Schemas\ParameterSchema;
use Emsifa\Evo\Swagger\Schemas\PathSchema;
use ReflectionParameter;
use ReflectionProperty;

interface OpenApiPathModifier
{
    public function modifyOpenApiPath(PathSchema $path);
}
