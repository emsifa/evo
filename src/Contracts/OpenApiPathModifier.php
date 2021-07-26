<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;

interface OpenApiPathModifier
{
    public function modifyOpenApiPath(Schema $path);
}
