<?php

namespace Emsifa\Evo\Casters;

use Emsifa\Evo\Contracts\Caster;
use Emsifa\Evo\Exceptions\CastErrorException;
use ReflectionParameter;
use ReflectionProperty;
use Stringable;

class StringCaster implements Caster
{
    public function cast($value, ReflectionProperty | ReflectionParameter $prop): mixed
    {
        $nullable = optional($prop->getType())->allowsNull();
        if ($nullable && is_null($value)) {
            return null;
        }

        if (is_string($value) || is_numeric($value)) {
            return (string) $value;
        }

        if (is_object($value) && $value instanceof Stringable) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? "true" : "false";
        }

        if (is_null($value)) {
            return "";
        }

        throw new CastErrorException("Cannot cast 'string' from type: ".gettype($value).'.');
    }
}
