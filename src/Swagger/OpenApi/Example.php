<?php

namespace Emsifa\Evo\Swagger\OpenApi;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiParameterModifier;
use Emsifa\Evo\Contracts\OpenApiSchemaModifier;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;

#[Attribute(Attribute::TARGET_PROPERTY + Attribute::TARGET_PARAMETER)]
class Example implements OpenApiSchemaModifier, OpenApiParameterModifier
{
    public function __construct(protected mixed $example)
    {
    }

    public function modifyOpenApiSchema(Schema $schema)
    {
        $schema->example = $this->example;
    }

    public function modifyOpenApiParameter(Parameter $parameter)
    {
        $parameter->example = $this->example;
    }
}
