<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class Server extends BaseSchema
{
    public string $url;
    public ?string $description;
    public ?array $variables;
}
