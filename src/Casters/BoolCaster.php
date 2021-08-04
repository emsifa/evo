<?php

namespace Emsifa\Evo\Casters;

use Emsifa\Evo\Contracts\Caster;
use Emsifa\Evo\Exceptions\CastErrorException;
use ReflectionParameter;
use ReflectionProperty;

class BoolCaster implements Caster
{
    public function cast($value, ReflectionProperty | ReflectionParameter $prop): mixed
    {
        $nullable = optional($prop->getType())->allowsNull();
        $truthy = [true, "true", 1, "1"];
        $falsy = [null, false, "false", 0, "0"];

        return match (true) {
            $nullable && is_null($value) => null,
            in_array($value, $truthy, true) => true,
            in_array($value, $falsy, true) => false,
            default => throw new CastErrorException("Cannot cast boolean from type: " . gettype($value)),
        };
    }
}
