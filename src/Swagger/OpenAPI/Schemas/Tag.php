<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

class Tag extends BaseSchema
{
    public string $name;
    public ?string $description = null;
    public ?ExternalDocs $externalDocs = null;
}
