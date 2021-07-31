<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class SecurityRequirement extends BaseSchema
{
    public function __construct(
        public string $key,
        public array $names,
    )
    {
    }

    public function toArray()
    {
        return $this->names;
    }
}
