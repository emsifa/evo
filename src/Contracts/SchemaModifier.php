<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenAPI\Schemas\Schema;

interface SchemaModifier
{
    public function modifySchema(Schema $schema);
}
