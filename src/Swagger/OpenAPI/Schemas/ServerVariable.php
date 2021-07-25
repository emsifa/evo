<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

class ServerVariable extends BaseSchema
{
    public ?array $enum;
    public string $default;
    public ?string $description;
}
