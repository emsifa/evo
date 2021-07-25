<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

class Header extends BaseSchema
{
    public ?string $description = null;
    public ?bool $required = null;
    public ?bool $deprecated = null;
    public ?bool $allowEmptyValue = null;
    public Schema | Reference $schema;
}
