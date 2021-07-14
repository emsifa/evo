<?php

namespace Emsifa\Evo\Swagger\Schemas;

use Illuminate\Contracts\Support\Arrayable;

class ExternalDocsSchema implements Arrayable
{
    public ?string $description = null;
    public string $url;

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
