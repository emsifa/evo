<?php

namespace Emsifa\Evo\Casters;

use Emsifa\Evo\Contracts\Caster;
use ReflectionParameter;
use ReflectionProperty;

class CollectionCaster extends ArrayCaster implements Caster
{
    public function cast($value, ReflectionProperty | ReflectionParameter $prop): mixed
    {
        $result = parent::cast($value, $prop);
        if (is_null($result)) {
            return null;
        }

        return collect($result);
    }
}
