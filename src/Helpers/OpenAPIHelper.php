<?php

namespace Emsifa\Evo\Helpers;

use Emsifa\Evo\Contracts\OpenApiSchemaModifier;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

class OpenApiHelper
{
    public static function makeParameterFromProperty(ReflectionProperty $property, string $name, string $in): Parameter
    {
        return new Parameter(
            name: $name,
            in: $in,
            schema: static::makeSchemaFromProperty($property),
            required: $property->hasDefaultValue() ? null : true,
        );
    }

    public static function makeParameterFromParameter(ReflectionParameter $parameter, string $name, string $in): Parameter
    {
        return new Parameter(
            name: $name,
            in: $in,
            schema: static::makeSchemaFromParameter($parameter),
            required: $parameter->isDefaultValueAvailable() ? null : true,
        );
    }

    public static function makeSchemaFromParameter(ReflectionParameter $parameter, bool $includeRequired = true): Schema
    {
        $type = $parameter->getType();
        $hasDefault = $parameter->isDefaultValueAvailable();
        $default = $hasDefault ? $parameter->getDefaultValue() : null;
        $nullable = $parameter->allowsNull();

        if ($type && ! $type->isBuiltin()) {
            $class = new ReflectionClass($type->getName());
            $schema = static::makeSchemaFromClass($class, $includeRequired);
        } else {
            $schema = new Schema(type: $type ? static::getType($type) : Schema::TYPE_STRING);
            $schema->default = $hasDefault ? $default : null;
            $schema->nullable = $nullable ?: null;
        }

        $modifiers = ReflectionHelper::getAttributesInstances(
            $parameter,
            OpenApiSchemaModifier::class,
            ReflectionAttribute::IS_INSTANCEOF,
        );

        foreach ($modifiers as $modifier) {
            $modifier->modifySchema($schema);
        }

        return $schema;
    }

    public static function makeSchemaFromProperty(ReflectionProperty $property, bool $includeRequired = true): Schema
    {
        $type = $property->getType();
        $hasDefault = $property->hasDefaultValue();
        $default = $hasDefault ? $property->getDefaultValue() : null;
        $nullable = $type ? $property->getType()->allowsNull() : true;

        if ($type && ! $type->isBuiltin()) {
            $class = new ReflectionClass($type->getName());
            $schema = static::makeSchemaFromClass($class, $includeRequired);
        } else {
            $schema = new Schema(type: $type ? static::getType($type) : Schema::TYPE_STRING);
            $schema->default = $hasDefault ? $default : null;
            $schema->nullable = $nullable ?: null;
        }

        $modifiers = ReflectionHelper::getAttributesInstances($property, OpenApiSchemaModifier::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($modifiers as $modifier) {
            $modifier->modifySchema($schema);
        }

        return $schema;
    }

    public static function makeSchemaFromClass(ReflectionClass $class, bool $includeRequired = true): Schema
    {
        $schema = new Schema(Schema::TYPE_OBJECT, classNameReference: $class->getName());
        $props = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        $requiredKeys = [];

        foreach ($props as $prop) {
            $name = $prop->getName();
            $propSchema = static::makeSchemaFromProperty($prop, $includeRequired);
            $schema->properties[$name] = $propSchema;
            if (! $prop->hasDefaultValue()) {
                $requiredKeys[] = $name;
            }
        }
        if ($includeRequired) {
            $schema->required = $requiredKeys;
        }

        $modifiers = ReflectionHelper::getAttributesInstances($class, OpenApiSchemaModifier::class, ReflectionAttribute::IS_INSTANCEOF);
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
