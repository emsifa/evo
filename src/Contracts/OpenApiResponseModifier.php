<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Swagger\OpenApi\Schemas\Response;

interface OpenApiResponseModifier
{
    public function modifyOpenApiResponse(Response $response);
}
