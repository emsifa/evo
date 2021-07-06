<?php

namespace Emsifa\Evo\Helpers;

use DateTime;
use Emsifa\Evo\Types\ArrayOf;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;

class ValidatorHelper
{
    public static function getRulesFromReflection(ReflectionClass | ReflectionProperty | ReflectionParameter $reflection, string $keyAlias = ''): array
    {
        if ($reflection instanceof ReflectionClass) {
            return static::getRulesFromClass($reflection);
        }

        return static::getRulesFromParameterOrProperty($reflection, $keyAlias);
    }

    public static function getRulesFromParameterOrProperty(ReflectionProperty | ReflectionParameter $reflection, string $keyAlias = '')
    {
        $keyName = $keyAlias ?: $reflection->getName();
        $rules = [];

        $type = $reflection->getType();
        if ($type) {
            $rules = array_merge($rules, static::getRulesFromTypeName($type->getName()));
            if ($type->getName() === "array" || is_a($type->getName(), Collection::class)) {
                $rules = array_merge($rules, static::getArrayItemRules($reflection));
            }
        }

        $rulesFromAttributes = ReflectionHelper::getAttributesInstances($reflection, Rule::class, ReflectionAttribute::IS_INSTANCEOF);
        if ($rulesFromAttributes) {
            $rules = array_merge($rules, $rulesFromAttributes);
        }

        return static::resolveRules($rules, $keyName);
    }

    public static function getRulesFromClass(ReflectionClass $class): array
    {
        $rules = [];
        $props = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            $key = $prop->getName();
            $propRules = static::getRulesFromReflection($prop);
            $rules = array_merge($rules, $propRules);
        }

        return $rules;
    }

    public static function getRulesFromTypeName(string $typeName): array
    {
        switch ($typeName) {
            case "int": return ["numeric"];
            case "float": return ["numeric"];
            case "string": return ["string"];
            case "array": return ["array"];
            case "bool": return ["boolean"];
            case DateTime::class: return ["date"];
        }

        if (class_exists($typeName)) {
            $classReflection = new ReflectionClass($typeName);

            return static::getRulesFromReflection($classReflection);
        }

        return [];
    }

    public static function getArrayItemRules(ReflectionProperty | ReflectionParameter $reflection): array
    {
        /**
         * @var ArrayOf $arrayOf
         */
        $arrayOf = ReflectionHelper::getFirstAttributeInstance($reflection, ArrayOf::class, ReflectionAttribute::IS_INSTANCEOF);
        if (! $arrayOf) {
            return [];
        }

        $itemTypeName = $arrayOf->getType();

        return static::resolveRules(static::getRulesFromTypeName($itemTypeName), "*");
    }

    protected static function resolveRules(array $otherRules, string $keyName): array
    {
        $keyRules = collect($otherRules)->filter(fn ($_, $key) => is_numeric($key));
        $childRules = collect($otherRules)->filter(fn ($_, $key) => ! is_numeric($key));

        $rules = [];
        if (count($keyRules)) {
            $rules[$keyName] = $keyRules->toArray();
        }

        foreach ($childRules as $childKey => $keyRules) {
            $rules[$keyName.".".$childKey] = $keyRules;
        }

        return $rules;
    }
}
