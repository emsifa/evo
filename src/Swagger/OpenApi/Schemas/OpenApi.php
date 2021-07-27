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

    public ?Components $components = null;

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
        if ($this->components) {
            $array["components"] = $this->components->toArray();
        }

        if ($this->servers) {
            foreach ($this->servers as $i => $server) {
                $array['servers'][$i] = $server->toArray();
            }
        }

        return $array;
    }
}
