<?php

namespace Emsifa\Evo\Helpers;

use Emsifa\Evo\Contracts\SchemaModifier;
use Emsifa\Evo\Swagger\OpenAPI\Schemas\Parameter;
use Emsifa\Evo\Swagger\OpenAPI\Schemas\Schema;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

class OpenAPIHelper
{
    public static function makeParameterFromProperty(ReflectionProperty $property, string $name, string $in): Parameter
    {
        return new Parameter(
            name: $name,
            in: $in,
            schema: static::makeSchemaFromProperty($property),
        );
    }

    public static function makeParameterFromParameter(ReflectionParameter $parameter, string $name, string $in): Parameter
    {
        return new Parameter(
            name: $name,
            in: $in,
            schema: static::makeSchemaFromParameter($parameter),
        );
    }

    public static function makeSchemaFromParameter(ReflectionParameter $parameter): Schema
    {
        $type = $parameter->getType();
        $default = $parameter->getDefaultValue();
        $nullable = $parameter->allowsNull();

        $schema = new Schema(type: $type ? static::getType($type) : Schema::TYPE_STRING);
        $schema->default = json_encode($default);
        $schema->nullable = $nullable ?: null;

        $modifiers = ReflectionHelper::getAttributesInstances($parameter, SchemaModifier::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($modifiers as $modifier) {
            $modifier->modifySchema($schema);
        }

        return $schema;
    }

    public static function makeSchemaFromProperty(ReflectionProperty $property): Schema
    {
        $type = $property->getType();
        $default = $property->getDefaultValue();
        $nullable = $property->getType()->allowsNull();

        $schema = new Schema(type: $type ? static::getType($type) : Schema::TYPE_STRING);
        $schema->default = json_encode($default);
        $schema->nullable = $nullable ?: null;

        $modifiers = ReflectionHelper::getAttributesInstances($property, SchemaModifier::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($modifiers as $modifier) {
            $modifier->modifySchema($schema);
        }

        return $schema;
    }

    protected static function getType(ReflectionNamedType $type): string
    {
        return match ($type->getName()) {
            "int" => Schema::TYPE_INTEGER,
            "float" => Schema::TYPE_NUMBER,
            "bool" => Schema::TYPE_BOOLEAN,
            "string" => Schema::TYPE_STRING,
            "array" => Schema::TYPE_ARRAY,
            default => Schema::TYPE_OBJECT,
        };
    }
}
