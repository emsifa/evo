<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

use Illuminate\Support\Arr;

class MediaType extends BaseSchema
{
    public string $type;
    public Schema|Reference $schema;
    public mixed $example = null;
    public ?array $examples = null;

    public function toArray()
    {
        return [
            $this->type => Arr::except(parent::toArray(), ['type'])
        ];
    }
}
