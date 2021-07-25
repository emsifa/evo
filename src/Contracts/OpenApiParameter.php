<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenAPI\Schemas\Parameter;
use ReflectionParameter;
use ReflectionProperty;

interface OpenApiParameter
{
    public function getOpenApiParameter(ReflectionParameter | ReflectionProperty $reflection): Parameter;
}
