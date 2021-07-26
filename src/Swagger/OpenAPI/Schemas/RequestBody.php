<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class RequestBody extends BaseSchema
{
    public ?string $description = null;
    public ?bool $required = null;
    /**
     * @var MediaType[]
     */
    public array $content = [];
}
