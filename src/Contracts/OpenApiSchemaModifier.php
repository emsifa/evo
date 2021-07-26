<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;

interface OpenApiSchemaModifier
{
    public function modifySchema(Schema $schema);
}
