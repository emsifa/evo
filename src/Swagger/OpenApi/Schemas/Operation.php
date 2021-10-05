<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class Operation extends BaseSchema
{
    /**
     * @var string[]
     */
    public ?array $tags = null;

    public ?string $summary = null;
    public ?string $description = null;
    public ?ExternalDocs $externalDocs = null;
    public ?string $operationId = null;

    /**
     * @var null|Parameter[]
     */
    public ?array $parameters = null;
    public RequestBody | Reference | null $requestBody = null;

    /**
     * @var array[string]Response
     */
    public array $responses = [];

    public ?bool $deprecated = null;

    /**
     * @var array[string]SecurityRequirement|null
     */
    public ?array $security = null;

    // public ?array $callbacks = null; // array[string]Callback|Reference
    // public ?array $servers = null; // Server

    public function toArray()
    {
        $array = parent::toArray();
        if (is_array($array['parameters'])) {
            foreach ($array['parameters'] as $i => $param) {
                $array['parameters'][$i] = $param->toArray();
            }
        }
        if (is_array($array['responses'])) {
            foreach ($array['responses'] as $type => $response) {
                $array['responses'][$type] = $response->toArray();
            }
        }
        if (is_array($this->security)) {
            $array['security'] = [];
            foreach ((array) $this->security as $key => $security) {
                $array['security'][] = $security->toArray();
            }
        }

        return $array;
    }
}
