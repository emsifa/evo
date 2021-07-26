<?php

namespace Emsifa\Evo\Helpers;

use Emsifa\Evo\Contracts\OpenApiSchemaModifier;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;
use Emsifa\Evo\Swagger\OpenApi\Schemas\PropertySchema;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;
use Illuminate\Http\UploadedFile;
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

    public static function makeSchemaFromParameter(ReflectionParameter $parameter): Schema
    {
        $type = $parameter->getType();
        $hasDefault = $parameter->isDefaultValueAvailable();
        $default = $hasDefault ? $parameter->getDefaultValue() : null;
        $nullable = $parameter->allowsNull();

        $schema = new Schema(type: $type ? static::getType($type) : Schema::TYPE_STRING);
        $schema->default = $hasDefault ? $default : null;
        $schema->nullable = $nullable ?: null;

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

    public static function makeSchemaFromProperty(ReflectionProperty $property): Schema
    {
        $type = $property->getType();
        $hasDefault = $property->hasDefaultValue();
        $default = $hasDefault ? $property->getDefaultValue() : null;
        $nullable = $type ? $property->getType()->allowsNull() : true;

        $schema = new Schema(type: $type ? static::getType($type) : Schema::TYPE_STRING);
        $schema->default = $hasDefault ? $default : null;
        $schema->nullable = $nullable ?: null;

        $modifiers = ReflectionHelper::getAttributesInstances($property, OpenApiSchemaModifier::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($modifiers as $modifier) {
            $modifier->modifySchema($schema);
        }

        return $schema;
    }

    public static function makeSchemaFromClass(ReflectionClass $class): Schema
    {
        $schema = new Schema(Schema::TYPE_OBJECT, classNameReference: $class->getName());
        $props = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        $requiredKeys = [];

        foreach ($props as $prop) {
            $name = $prop->getName();
            $propSchema = static::makeSchemaFromProperty($prop);
            $schema->properties[$name] = $propSchema;
            if (!$prop->hasDefaultValue()) {
                $requiredKeys[] = $name;
            }
        }
        $schema->required = $requiredKeys;

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
