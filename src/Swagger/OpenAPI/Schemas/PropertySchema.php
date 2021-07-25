<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

use Illuminate\Support\Arr;

class PropertySchema extends Schema
{
    public string $key;

    public function toArray()
    {
        return [$this->key => Arr::except(parent::toArray(), ['key'])];
    }
}
