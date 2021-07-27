<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class Reference extends BaseSchema
{
    public string $ref;

    public function toArray()
    {
        return ['$ref' => $this->ref];
    }
}
