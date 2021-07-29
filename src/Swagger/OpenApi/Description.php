<?php

namespace Emsifa\Evo\Swagger\OpenApi;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiOperationModifier;
use Emsifa\Evo\Contracts\OpenApiParameterModifier;
use Emsifa\Evo\Contracts\OpenApiRequestBodyModifier;
use Emsifa\Evo\Contracts\OpenApiResponseModifier;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Operation;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;
use Emsifa\Evo\Swagger\OpenApi\Schemas\RequestBody;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Response;

#[Attribute]
class Description implements
    OpenApiRequestBodyModifier,
    OpenApiParameterModifier,
    OpenApiOperationModifier,
    OpenApiResponseModifier
{
    public function __construct(protected string $description)
    {
    }

    public function modifyOpenApiRequestBody(RequestBody $body, mixed $reflection = null)
    {
        $body->description = $this->description;
    }

    public function modifyOpenApiParameter(Parameter $parameter)
    {
        $parameter->description = $this->description;
    }

    public function modifyOpenApiOperation(Operation $operation)
    {
        $operation->description = $this->description;
    }

    public function modifyOpenApiResponse(Response $response)
    {
        $response->description = $this->description;
    }
}
