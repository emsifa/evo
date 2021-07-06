<?php

namespace Emsifa\Evo\Casters;

use Emsifa\Evo\Contracts\Caster;
use Emsifa\Evo\Exceptions\CastErrorException;
use ReflectionParameter;
use ReflectionProperty;

class FloatCaster implements Caster
{
    public function cast($value, ReflectionProperty|ReflectionParameter $prop): mixed
    {
        $nullable = optional($prop->getType())->allowsNull();
        if ($nullable && is_null($value)) {
            return null;
        }

        if (is_numeric($value) || is_null($value)) {
            return (float) $value;
        }

        throw new CastErrorException("Cannot cast 'float' from type: ".gettype($value).'.');
    }
}
