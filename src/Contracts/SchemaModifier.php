<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;
use ReflectionParameter;
use ReflectionProperty;

interface SchemaModifier
{
    public function modifySchema(Schema $schema);
}
