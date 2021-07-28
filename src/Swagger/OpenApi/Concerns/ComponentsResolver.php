<?php

namespace Emsifa\Evo\Swagger\OpenApi\Concerns;

use Emsifa\Evo\Swagger\OpenApi\Schemas\Components;
use Emsifa\Evo\Swagger\OpenApi\Schemas\MediaType;
use Emsifa\Evo\Swagger\OpenApi\Schemas\OpenApi;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Operation;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Path;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Reference;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Response;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;
use Illuminate\Support\Arr;

trait ComponentsResolver
{
    public function resolveComponents(OpenApi $openApi)
    {
        /**
         * @var Path $path
         */
        foreach ($openApi->paths as $uri => $path) {
            /**
             * @var Operation[] $operations
             */
            $operations = collect([
                $path->get,
                $path->post,
                $path->put,
                $path->patch,
                $path->delete,
            ])->filter(fn ($path) => ! is_null($path));

            foreach ($operations as $operation) {
                $this->collectComponentsFromOperation($operation, $openApi);
            }
        }
    }

    public function collectComponentsFromOperation(Operation $operation, OpenApi $openApi)
    {
        $requestBody = $operation->requestBody;
        /**
         * @var MediaType|null $requestBodyContent
         */
        $requestBodyContent = $requestBody ? Arr::first($requestBody->content) : null;
        if ($requestBody && ! $this->componentHasCollected($requestBodyContent->schema, $openApi)) {
            $ref = $this->collectComponent($requestBodyContent->schema, $openApi);
            $requestBodyContent->schema = $ref;
        }
        /**
         * @var Response $response
         */
        foreach ($operation->responses as $status => $response) {
            /**
             * @var MediaType $content
             */
            $content = Arr::first($response->content);
            if (! $this->componentHasCollected($content->schema, $openApi)) {
                $ref = $this->collectComponent($content->schema, $openApi);
                $content->schema = $ref;
            }
        }
    }

    public function componentHasCollected(Schema $schema, OpenApi $openApi): bool
    {
        $refClassName = $schema->getClassNameReference();
        $refName = $this->resolveSchemaReferenceName($refClassName);

        return $openApi->components instanceof Components
            && is_array($openApi->components->schemas)
            && array_key_exists($refName, $openApi->components->schemas);
    }

    public function collectComponent(Schema $schema, OpenApi $openApi): Reference
    {
        $refClassName = $schema->getClassNameReference();
        $refName = $this->resolveSchemaReferenceName($refClassName);

        if (! $openApi->components) {
            $openApi->components = new Components;
        }
        $openApi->components->schemas[$refName] = clone $schema;

        $ref = new Reference;
        $ref->ref = "#/components/schemas/{$refName}";

        return $ref;
    }

    public function resolveSchemaReferenceName(string $className): string
    {
        return str_replace("\\", ".", $className);
    }
}
