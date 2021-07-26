<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;

interface SchemaModifier
{
    public function modifySchema(Schema $schema);
}
