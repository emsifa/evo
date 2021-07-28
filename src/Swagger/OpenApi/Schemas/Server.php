<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class Server extends BaseSchema
{
    public string $url;
    public ?string $description = null;
    public ?array $variables = null;
}
