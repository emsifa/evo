<?php

namespace Emsifa\Evo\Swagger\OpenApi;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiOperationModifier;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Operation;

#[Attribute(Attribute::TARGET_METHOD)]
class Summary implements OpenApiOperationModifier
{
    public function __construct(protected string $summary)
    {
    }

    public function modifyOpenApiOperation(Operation $operation)
    {
        $operation->summary = $this->summary;
    }
}
