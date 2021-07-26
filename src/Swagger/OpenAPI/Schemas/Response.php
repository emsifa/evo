<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class Response extends BaseSchema
{
    public ?string $description = null;

    /**
     * @var array[string]Reference|Header
     */
    public ?array $headers = null;

    /**
     * @var array[string]MediaType
     */
    public ?array $content = null;

    public function toArray()
    {
        $array = parent::toArray();
        if ($this->content) {
            foreach ($this->content as $type => $schema) {
                $array["content"][$type] = $schema->toArray();
            }
        }
        return $array;
    }
}
