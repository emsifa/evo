<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

class Parameter extends BaseSchema
{
    const IN_QUERY = "query";
    const IN_HEADER = "header";
    const IN_PATH = "path";
    const IN_COOKIE = "cookie";

    public function __construct(
        public string $name,
        public string $in,
        public ?string $description = null,
        public ?bool $required = null,
        public ?bool $deprecated = null,
        public ?bool $allowEmptyValue = null,
        public Schema | Reference $schema,
    ) {
    }
}
