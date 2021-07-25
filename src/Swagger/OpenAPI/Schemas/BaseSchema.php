<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

use Emsifa\Evo\Helpers\ObjectHelper;
use Illuminate\Contracts\Support\Arrayable;

abstract class BaseSchema implements Arrayable
{
    public function toArray()
    {
        $schema = ObjectHelper::toArray($this, true);

        return collect($schema)
            ->filter(fn ($value) => ! is_null($value))
            ->toArray();
    }
}
