<?php

namespace Emsifa\Evo\Swagger\Schemas;

use Illuminate\Contracts\Support\Arrayable;

class PathSchema implements Arrayable
{
    /**
     * @var string|null
     */
    public ?string $summary = null;

    /**
     * @var string|null
     */
    public ?string $description = null;

    /**
     * @var string[]
     */
    public array $tags = [];

    /**
     * @var string
     */
    public string $operationId = '';

    /**
     * @var ParameterSchema[]
     */
    public array $parameters = [];

    /**
     * @var RequestBody|null
     */
    public ?RequestBodySchema $requestBody = null;

    /**
     * @var ResponseSchema[]
     */
    public array $responses = [];

    /**
     * @var ExternalDocsSchema|null
     */
    public ?ExternalDocsSchema $externalDocs = null;

    /**
     * @var bool
     */
    public bool $deprecated = false;

    public function toArray()
    {
        $schema = [];
        if ($this->summary) {
            $schema['summary'] = $this->summary;
        }
        if ($this->description) {
            $schema['description'] = $this->description;
        }
        if (count($this->tags)) {
            $schema['tags'] = $this->tags;
        }

        $schema['operationId'] = $this->operationId;

        if (count($this->parameters)) {
            $schema['parameters'] = collect($this->parameters)->map(fn ($param) => $param->toArray())->toArray();
        }

        if ($this->requestBody) {
            $schema['requestBody'] = $this->requestBody->toArray();
        }

        if ($this->responses) {
            $schema['responses'] = [];
            foreach ($this->responses as $response) {
                $schema['responses'][$response->getStatus()] = $response->toArray();
            }
        }

        if ($this->externalDocs) {
            $schema['externalDocs'] = $this->externalDocs->toArray();
        }

        return $schema;
    }
}
