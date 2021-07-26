<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class Response extends BaseSchema
{
    public string $description;

    /**
     * @var array[string]Reference|Header
     */
    public ?array $headers = null;

    /**
     * @var array[string]MediaType
     */
    public ?array $content = null;
}
