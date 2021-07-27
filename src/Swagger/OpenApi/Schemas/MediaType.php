<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class MediaType extends BaseSchema
{
    public function __construct(
        public Schema | Reference $schema,
        public mixed $example = null,
        public ?array $examples = null,
    ) {
    }
}
