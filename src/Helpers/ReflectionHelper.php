<?php

namespace Emsifa\Evo\Helpers;

use ReflectionAttribute;
use ReflectionClass;

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
     * @param  string|null $name
     * @param  int $flags
     * @return bool
     */
    public static function hasAttribute($reflection, ?string $name, int $flags = 0): bool
    {
        return count($reflection->getAttributes($name, $flags)) > 0;
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
