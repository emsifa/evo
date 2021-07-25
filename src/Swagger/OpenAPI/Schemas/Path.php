<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

class Path extends BaseSchema
{
    public ?string $summary = null;
    public ?string $description = null;
    public ?Operation $get;
    public ?Operation $put;
    public ?Operation $post;
    public ?Operation $delete;
    public ?Operation $patch;
    public ?Operation $options;
    public ?Operation $head;
    public ?Operation $trace;
}
