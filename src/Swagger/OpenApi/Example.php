<?php

namespace Emsifa\Evo\Swagger\OpenApi;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiSchemaModifier;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Example implements OpenApiSchemaModifier
{
    public function __construct(protected mixed $example)
    {
    }

    public function modifyOpenApiSchema(Schema $schema)
    {
        $schema->example = $this->example;
    }
}
