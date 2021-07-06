<?php

namespace Emsifa\Evo\Contracts;

use ReflectionParameter;
use ReflectionProperty;

interface Caster
{
    public function cast($value, ReflectionProperty | ReflectionParameter $prop): mixed;
}
