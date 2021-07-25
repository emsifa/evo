<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

class Operation extends BaseSchema
{
    /**
     * @var string[]
     */
    public ?array $tags = null;

    public ?string $summary = null;
    public ?string $description = null;
    public ?ExternalDocs $externalDocs = null;
    public ?string $operationId = null;
    public ?Parameter $parameters = null;
    public RequestBody|Reference|null $requestBody = null;

    /**
     * @var array[string]Response
     */
    public array $responses = [];

    public ?bool $deprecated = null;

    /**
     * @var SecurityRequirement[]|null
     */
    public ?array $security = null;

    // public ?array $callbacks = null; // array[string]Callback|Reference
    // public ?array $servers = null; // Server
}
