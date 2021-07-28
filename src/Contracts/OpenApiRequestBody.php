<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenApi\Schemas\RequestBody;
use ReflectionParameter;
use ReflectionProperty;

interface OpenApiRequestBody
{
    public function getOpenApiRequestBody(ReflectionParameter | ReflectionProperty $reflection): RequestBody;
}
