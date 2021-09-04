<?php

namespace Emsifa\Evo\Helpers;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;

class ReflectionHelper
{
    /**
     * Get class (including its parents) attributes
     *
     * @param  string|object $object
     * @param  string|null $attrName
     * @param  int $flags
     * @return \ReflectionAttribute[]
     */
    public static function getClassAttributes($object, ?string $attrName = null, int $flags = 0): array
    {
        $classes = [$object, ...static::getParents($object)];
        $attributes = [];
        foreach ($classes as $class) {
            $reflection = new ReflectionClass($class);
            $attributes = [...$attributes, ...$reflection->getAttributes($attrName, $flags)];
        }

        return $attributes;
    }

    /**
     * Get class (including its parents) attribute instances
     *
     * @param  ReflectionClass $class
     * @param  string|null $attrName
     * @param  int $flags
     * @return \ReflectionAttribute[]
     */
    public static function getClassAttributeInstances(ReflectionClass $class, ?string $attrName = null, int $flags = 0): array
    {
        $attributes = static::getClassAttributes($class->getName(), $attrName, $flags);

        return array_map(fn ($attr) => $attr->newInstance(), $attributes);
    }

    public static function getFirstClassAttribute($object, ?string $attrName = null, int $flags = 0): ?ReflectionAttribute
    {
        $attributes = static::getClassAttributes($object, $attrName, $flags);

        return count($attributes) ? $attributes[0] : null;
    }

    /**
     * @param  \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter $reflection
     * @param  string|null $name
     * @param  int $flags
     * @return mixed
     */
    public static function getAttributesInstances($reflection, ?string $name = null, int $flags = 0): mixed
    {
        $attributes = $reflection->getAttributes($name, $flags);

        return array_map(fn ($attr) => $attr->newInstance(), $attributes);
    }

    /**
     * @param  \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter $reflection
     * @param  string|null $name
     * @param  int $flags
     * @return mixed
     */
    public static function getFirstAttributeInstance($reflection, ?string $name = null, int $flags = 0): mixed
    {
        $attributes = $reflection->getAttributes($name, $flags);

        return count($attributes) ? $attributes[0]->newInstance() : null;
    }

    /**
     * @param  \ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter $reflection
     * @param  array|string|null $name
     * @param  int $flags
     * @return bool
     */
    public static function hasAttribute($reflection, array | string | null $name = null, int $flags = 0): bool
    {
        if (is_array($name)) {
            foreach ($name as $n) {
                if (static::hasAttribute($reflection, $n, $flags)) {
                    return true;
                }
            }

            return false;
        }

        return count($reflection->getAttributes($name, $flags)) > 0;
    }

    public static function unionHasType(ReflectionUnionType $type, string $typeName): bool
    {
        foreach ($type->getTypes() as $type) {
            $name = $type->getName();
            if (is_a($name, $typeName, true)) {
                return true;
            }
        }

        return false;
    }

    public static function hasDefaultValue(ReflectionProperty | ReflectionParameter $reflection): bool
    {
        return $reflection instanceof ReflectionProperty
            ? $reflection->hasDefaultValue()
            : $reflection->isDefaultValueAvailable();
    }

    public static function getDefaultValue(ReflectionProperty | ReflectionParameter $reflection): mixed
    {
        return $reflection->getDefaultValue();
    }

    private static function getParents($class): array
    {
        $parents = [];
        while ($parent = get_parent_class($class)) {
            $parents[] = $parent;
            $class = $parent;
        }

        return $parents;
    }
}
