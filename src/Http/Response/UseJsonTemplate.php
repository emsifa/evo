<?php

namespace Emsifa\Evo\Http\Response;

use Attribute;
use Emsifa\Evo\Contracts\JsonData;
use Emsifa\Evo\Contracts\OpenApiSchemaModifier;
use Emsifa\Evo\Helpers\OpenApiHelper;
use Emsifa\Evo\Helpers\TypeHelper;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;
use ReflectionClass;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_CLASS)]
class UseJsonTemplate implements OpenApiSchemaModifier
{
    protected array $properties;

    public function __construct(
        protected string $templateClassName,
        mixed ...$properties,
    )
    {
        $this->properties = $properties;
    }

    public function getTemplateClassName(): string
    {
        return $this->templateClassName;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function modifyOpenApiSchema(Schema $schema)
    {
        $originalSchema = clone $schema;
        $schema->properties = [];

        $class = new ReflectionClass($this->templateClassName);
        $props = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            $name = $prop->getName();
            $typeName = $prop->getType() ? $prop->getType()->getName() : null;
            if ($typeName && ! TypeHelper::isBuiltInType($typeName) && is_a($typeName, JsonData::class, true)) {
                $schema->properties[$name] = $originalSchema;
            } else {
                $propSchema = OpenApiHelper::makeSchemaFromProperty($prop, false);
                $schema->properties[$name] = $propSchema;
            }
        }
    }
}
