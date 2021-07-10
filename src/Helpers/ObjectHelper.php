<?php

namespace Emsifa\Evo\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use ReflectionClass;
use ReflectionProperty;

class ObjectHelper
{
    public static function toArray(object $object, bool $castArrayable = true): array
    {
        $result = [];
        $props = (new ReflectionClass($object))->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            $name = $prop->getName();
            $value = $prop->getValue($object);
            if (is_object($value) && $value instanceof Arrayable && $castArrayable) {
                $result[$name] = $value->toArray();
            } else {
                $result[$name] = $value;
            }
        }
        return $result;
    }
}
