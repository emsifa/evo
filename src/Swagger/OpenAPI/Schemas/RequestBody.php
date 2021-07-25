<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

use Illuminate\Contracts\Support\Arrayable;

class RequestBody implements Arrayable
{
    public ?string $description = null;
    public bool $required = true;
    public string $contentType = "*/*";
    public string $contentSchema;

    public function toArray()
    {
        $schema = [];
        if ($this->description) {
            $schema['description'] = $this->description;
        }
        $schema['required'] = $this->required;
        $schema['url'] = $this->url;
        $schema['content'][$this->contentType] = [
            'schema' => [
                '$ref' => $this->contentSchema,
            ],
        ];

        return $schema;
    }
}
