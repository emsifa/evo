<?php

namespace Emsifa\Evo\Casters;

use Emsifa\Evo\Contracts\Caster;
use Emsifa\Evo\Exceptions\CastErrorException;
use ReflectionParameter;
use ReflectionProperty;

class DateTimeCaster implements Caster
{
    public function cast($value, ReflectionProperty | ReflectionParameter $prop): mixed
    {
        $nullable = optional($prop->getType())->allowsNull();
        if ($nullable && is_null($value)) {
            return null;
        }

        if (is_string($value) && $result = date_create($value)) {
            return $result;
        }

        throw new CastErrorException("Cannot cast 'int' from type: ".gettype($value).'.');
    }
}
