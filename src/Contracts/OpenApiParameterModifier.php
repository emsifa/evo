<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;

interface OpenApiParameterModifier
{
    public function modifyOpenApiParameter(Parameter $parameter);
}
