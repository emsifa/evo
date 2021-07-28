<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class RequestBody extends BaseSchema
{
    public ?string $description = null;
    public ?bool $required = null;
    /**
     * @var MediaType[]
     */
    public array $content = [];

    public function toArray()
    {
        $array = parent::toArray();
        foreach ($this->content as $type => $content) {
            $array["content"][$type] = $content->toArray();
        }

        return $array;
    }
}
