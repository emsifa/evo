<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class Components extends BaseSchema
{
    public ?array $schemas = null;
    public ?array $responses = null;
    public ?array $parameters = null;
    public ?array $examples = null;
    public ?array $requestBodies = null;
    public ?array $headers = null;
    public ?array $securitySchemes = null;
    public ?array $links = null;
    public ?array $callbacks = null;

    public function toArray()
    {
        $array = parent::toArray();
        foreach ($array as $key => $values) {
            if (is_array($values)) {
                foreach ($values as $i => $value) {
                    $array[$key][$i] = $value->toArray();
                }
            }
        }

        return $array;
    }
}
