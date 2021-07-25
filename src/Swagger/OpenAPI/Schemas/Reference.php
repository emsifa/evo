<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

class Reference extends BaseSchema
{
    public string $ref;

    public function toArray()
    {
        return ['$ref' => $this->ref];
    }
}
