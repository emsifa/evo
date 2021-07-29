<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;

interface OpenApiParameterModifier
{
    public function modifyOpenApiParameter(Parameter $parameter);
}
