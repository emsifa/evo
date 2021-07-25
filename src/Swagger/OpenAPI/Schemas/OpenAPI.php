<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

class OpenAPI extends BaseSchema
{
    public string $openapi;
    public Info $info;

    /**
     * @var Server[]
     */
    public ?array $servers = null;

    /**
     * @var array[string]Path
     */
    public array $paths = [];

    public ?array $components = null;

    /**
     * @var SecurityRequirement[]
     */
    public ?array $security = null;

    /**
     * @var Tag[]
     */
    public ?array $tags = null;

    public ?ExternalDocs $externalDocs;
}
