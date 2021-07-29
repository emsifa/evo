<?php

namespace Emsifa\Evo\Swagger\OpenApi;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiParameterModifier;
use Emsifa\Evo\Contracts\OpenApiRequestBodyModifier;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;
use Emsifa\Evo\Swagger\OpenApi\Schemas\RequestBody;

#[Attribute(Attribute::TARGET_PARAMETER + Attribute::TARGET_CLASS + Attribute::TARGET_PROPERTY)]
class Description implements OpenApiRequestBodyModifier, OpenApiParameterModifier
{
    public function __construct(protected string $description)
    {
    }

    public function modifyOpenApiRequestBody(RequestBody $body, mixed $reflection = null)
    {
        $body->description = $this->description;
    }

    public function modifyOpenApiParameter(Parameter $parameter, mixed $reflection = null)
    {
        $parameter->description = $this->description;
    }
}
