<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class OpenApi extends BaseSchema
{
    public string $openapi;
    public Info $info;

    /**
     * @var Server[]
     */
    public ?array $servers = null;

    /**
     * @var array[string]Path
     */
    public array $paths = [];

    public ?array $components = null;

    /**
     * @var SecurityRequirement[]
     */
    public ?array $security = null;

    /**
     * @var Tag[]
     */
    public ?array $tags = null;

    public ?ExternalDocs $externalDocs = null;

    public function toArray()
    {
        $array = parent::toArray();
        foreach ($array['paths'] as $key => $path) {
            $array['paths'][$key] = $path->toArray();
        }

        return $array;
    }
}
