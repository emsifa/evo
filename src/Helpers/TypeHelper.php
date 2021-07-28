<?php

namespace Emsifa\Evo\Helpers;

class TypeHelper
{
    public static function isBuiltInType(string $typeName)
    {
        return match (strtolower($typeName)) {
            "int", "float", "bool", "string", "array", "object" => true,
            default => false,
        };
    }
}
