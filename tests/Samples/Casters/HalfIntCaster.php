<?php

namespace Emsifa\Evo\Tests\Samples\Casters;

use Attribute;
use Emsifa\Evo\Contracts\Caster;
use Emsifa\Evo\Exceptions\CastErrorException;
use ReflectionParameter;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class HalfIntCaster implements Caster
{
    public function cast($value, ReflectionProperty|ReflectionParameter $prop): mixed
    {
        if (is_numeric($value)) {
            return $value / 2;
        }

        throw new CastErrorException("HalfIntCaster error");
    }
}
