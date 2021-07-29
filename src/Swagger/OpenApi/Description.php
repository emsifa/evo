<?php

namespace Emsifa\Evo\Swagger\OpenApi;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiRequestBodyModifier;
use Emsifa\Evo\Swagger\OpenApi\Schemas\RequestBody;

#[Attribute(Attribute::TARGET_PARAMETER + Attribute::TARGET_CLASS + Attribute::TARGET_PROPERTY)]
class Description implements OpenApiRequestBodyModifier
{
    public function __construct(protected string $description)
    {
    }

    public function modifyOpenApiRequestBody(RequestBody $body, mixed $reflection = null)
    {
        $body->description = $this->description;
    }
}
