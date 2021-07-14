<?php

namespace Emsifa\Evo\Swagger\Schemas;

use Illuminate\Contracts\Support\Arrayable;

class ResponseSchema implements Arrayable
{
    public int $status;
    public string $description = "";
    public array $headers = [];

    public ?string $contentType = null;
    public string $type = "";
    public bool $isArray = false;

    public function getStatus()
    {
        return $this->status;
    }

    public function toArray()
    {
        $schema = [];
        if ($this->description) {
            $schema['description'] = $this->description;
        }
        $schema['url'] = $this->url;
        return $schema;
    }
}
