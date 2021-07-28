<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class Tag extends BaseSchema
{
    public string $name;
    public ?string $description = null;
    public ?ExternalDocs $externalDocs = null;
}
