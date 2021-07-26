<?php

namespace Emsifa\Evo\Http\Concerns;

use Emsifa\Evo\Contracts\OpenApiRequestBodyModifier;
use Emsifa\Evo\Helpers\OpenApiHelper;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Swagger\OpenApi\Schemas\RequestBody;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;

trait OpenApiRequestBodyMaker
{
    public function getOpenApiRequestBody(ReflectionProperty | ReflectionParameter $reflection): RequestBody
    {
        $className = $this->getRequestBodyTypeClassName($reflection);
        $reflectionClass = $className ? new ReflectionClass($className) : null;

        $body = new RequestBody;
        $body->required = $this->isRequestBodyRequired($reflection);
        $body->description = $this->getRequestBodyDescription($reflection, $reflectionClass);
        $body->content = $reflectionClass
            ? $this->getRequestBodyContent($reflection, $reflectionClass)
            : $this->getDefaultRequestBodyContent();

        /**
         * @var OpenApiRequestBodyModifier[] $modifiers
         */
        $modifiers = [
            ...ReflectionHelper::getAttributesInstances(
                $reflection,
                OpenApiRequestBodyModifier::class,
                ReflectionAttribute::IS_INSTANCEOF,
            ),
            ...($reflectionClass
                ? ReflectionHelper::getAttributesInstances(
                    $reflectionClass,
                    OpenApiRequestBodyModifier::class,
                    ReflectionAttribute::IS_INSTANCEOF,
                )
                : []
            ),
        ];
        foreach ($modifiers as $modifier) {
            $modifier->modifyOpenApiRequestBody($body);
        }

        return $body;
    }

    protected function getRequestBodyTypeClassName(ReflectionParameter | ReflectionProperty $reflection): ?string
    {
        $type = $reflection->getType();
        return $type && !$type->isBuiltin() ? $type->getName() : null;
    }

    protected function isRequestBodyRequired(ReflectionProperty | ReflectionParameter $reflection): ?bool
    {
        $required = $reflection instanceof ReflectionProperty
            ? !$reflection->hasDefaultValue()
            : !$reflection->isDefaultValueAvailable();

        return $required ?: null;
    }

    protected function getRequestBodyDescription(
        ReflectionProperty | ReflectionParameter $paramOrProp,
        ?ReflectionClass $class = null
    ): ?string
    {
        return null;
    }

    protected function getRequestBodyContent(
        ReflectionParameter | ReflectionProperty $paramOrProp,
        ReflectionClass $class,
    ): array
    {
        $hasFile = $this->isRequestBodyHasFile($class);
        $schema = OpenApiHelper::makeSchemaFromClass($class);
        $contentType = $hasFile ? "multipart/form-data" : "application/json";
        return [$contentType => $schema];
    }

    protected function getDefaultRequestBodyContent(): array
    {
        return ["*/*" => new Schema("object", properties: [])];
    }

    protected function isRequestBodyHasFile(ReflectionClass $class): bool
    {
        $props = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            $typeName = $prop->getType() ? $prop->getType()->getName() : null;
            if ($typeName && is_a($typeName, UploadedFile::class)) {
                return true;
            }
        }
        return false;
    }
}
