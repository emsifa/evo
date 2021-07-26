<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenApi\Schemas\RequestBody;

interface OpenApiRequestBodyModifier
{
    public function modifyOpenApiRequestBody(RequestBody $body);
}
