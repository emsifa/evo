<?php

namespace Emsifa\Evo\Casters;

use Emsifa\Evo\Contracts\Caster;
use ReflectionParameter;
use ReflectionProperty;

class BoolCaster implements Caster
{
    public function cast($value, ReflectionProperty | ReflectionParameter $prop): mixed
    {
        $nullable = optional($prop->getType())->allowsNull();
        if ($nullable && is_null($value)) {
            return null;
        }

        return (bool) $value;
    }
}
