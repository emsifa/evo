<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenApi\Schemas\Operation;

interface OpenApiOperationModifier
{
    public function modifyOpenApiOperation(Operation $operation);
}
