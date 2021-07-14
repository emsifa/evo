<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\Schemas\ParameterSchema;
use ReflectionParameter;
use ReflectionProperty;

interface OpenApiParameter
{
    public function getOpenApiParameter(ReflectionParameter|ReflectionProperty $reflection): ParameterSchema;
}
