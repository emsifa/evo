<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class Path extends BaseSchema
{
    public ?string $summary = null;
    public ?string $description = null;
    public ?Operation $get = null;
    public ?Operation $put = null;
    public ?Operation $post = null;
    public ?Operation $delete = null;
    public ?Operation $patch = null;
    public ?Operation $options = null;
    public ?Operation $head = null;
    public ?Operation $trace = null;
}
