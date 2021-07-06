<?php

namespace Emsifa\Evo;

use Emsifa\Evo\Contracts\Caster;
use Emsifa\Evo\DTO\CacheCasters;
use Emsifa\Evo\DTO\CastWith;
use Emsifa\Evo\DTO\UseCaster;
use Emsifa\Evo\Exceptions\UndefinedCasterException;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Illuminate\Support\Arr;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

class ObjectFiller
{
    protected static array $castersCache = [];

    public static function getCachedCasters(): array
    {
        return static::$castersCache;
    }

    public static function fillObject(object &$obj, array $data): void
    {
        $casters = static::getCasters(get_class($obj));
        $props = (new ReflectionClass($obj))->getProperties();
        foreach ($props as $prop) {
            $value = static::getValue($prop, $data, $casters);
            $prop->setValue($obj, $value);
        }
    }

    public static function getValue(ReflectionProperty $prop, array &$data, array $casters)
    {
        $name = $prop->getName();
        $type = $prop->getType();
        $nullable = $type ? $type->allowsNull() : true;
        $typeName = $type ? $type->getName() : null;

        $value = Arr::get($data, $name, $prop->getDefaultValue());

        $castWith = ReflectionHelper::getFirstAttributeInstance($prop, CastWith::class, ReflectionAttribute::IS_INSTANCEOF);
        $propCaster = $castWith ? $castWith->getCaster() : null;

        if (!$propCaster && $typeName) {
            $propCaster = Arr::get($casters, $typeName);
        }

        if ($propCaster) {
            /**
             * @var Caster $caster
             */
            $caster = is_string($propCaster) ? new $propCaster : $propCaster;
            return $caster->cast($value, $prop);
        }

        if (!$typeName) {
            return $value;
        }

        if (is_array($value) && class_exists($typeName)) {
            $object = new $typeName;
            static::fillObject($object, $value);
            return $object;
        }

        if (is_null($value) && $nullable) {
            return $value;
        }

        if (static::isTypeOf($value, $typeName)) {
            return $value;
        }

        $class = $prop->getDeclaringClass()->getName();

        throw new UndefinedCasterException("Cannot cast value to '{$class}::{$name}' property. Undefined caster for type/class '{$typeName}'.");
    }

    public static function getCasters(string $class)
    {
        $cacheCaster = ReflectionHelper::hasAttribute(new ReflectionClass($class), CacheCasters::class);
        if ($cacheCaster && isset(static::$castersCache[$class])) {
            return static::$castersCache[$class];
        }

        $attributes = ReflectionHelper::getClassAttributes($class, UseCaster::class);
        $casters = [];
        foreach ($attributes as $attr) {
            /**
             * @var UseCaster $useCaster
             */
            $useCaster = $attr->newInstance();
            $type = $useCaster->getType();
            if (!array_key_exists($type, $casters)) {
                $casterClass = $useCaster->getCaster();
                $casters[$type] = new $casterClass;
            }
        }

        if ($cacheCaster) {
            static::$castersCache[$class] = $casters;
        }

        return $casters;
    }

    private static function isTypeOf($value, string $typeName): bool
    {
        $type = gettype($value);

        switch ($type) {
            case "integer": return $typeName === "int";
            case "double": return $typeName === "float";
            case "string": return $typeName === "string";
            case "boolean": return $typeName === "bool";
            case "array": return $typeName === "array";
            case "object": return $value instanceof $typeName;
        }

        return false;
    }
}
