<?php

namespace Emsifa\Evo\Swagger\OpenApi;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiOperationModifier;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Operation;

#[Attribute(Attribute::TARGET_METHOD + Attribute::TARGET_CLASS)]
class Tags implements OpenApiOperationModifier
{
    protected array $tags;

    public function __construct(string ...$tags)
    {
        $this->tags = $tags;
    }

    public function modifyOpenApiOperation(Operation $operation)
    {
        $operation->tags = [...($operation->tags ?? []), ...$this->tags];
    }
}
