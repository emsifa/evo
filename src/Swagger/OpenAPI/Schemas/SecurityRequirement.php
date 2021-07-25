<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

class SecurityRequirement extends BaseSchema
{
    public string $key;
    public array $names;

    public function toArray()
    {
        return [$this->key => $this->names];
    }
}
