<?php

namespace Emsifa\Evo\Swagger\Schemas;

use Illuminate\Contracts\Support\Arrayable;

class ParameterSchema implements Arrayable
{
    public string $name;
    public string $in;
    public ?string $description = null;
    public bool $required = false;
    public string $type;
    public ?string $format;
    public bool $isArray = false;

    public function toArray()
    {
        $schema = [];
        $schema['name'] = $this->name;
        $schema['in'] = $this->in;
        if ($this->description) {
            $schema['description'] = $this->description;
        }
        $schema['required'] = $this->required;

        $schema['schema'] = [
            'type' => $this->isArray ? "array" : $this->type,
        ];

        if ($this->format) {
            $schema['schema']['format'] = $this->format;
        }

        if ($this->isArray) {
            $schema['schema'] = [
                'type' => 'array',
                'items' => $schema['schema']
            ];
        }

        return $schema;
    }
}
