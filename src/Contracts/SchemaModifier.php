<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenAPI\Schemas\Parameter;
use Emsifa\Evo\Swagger\OpenAPI\Schemas\Schema;
use ReflectionParameter;
use ReflectionProperty;

interface SchemaModifier
{
    public function modifySchema(Schema $schema);
}
