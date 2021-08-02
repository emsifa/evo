<?php

namespace Emsifa\Evo\Contracts;

use Faker\Generator;
use ReflectionProperty;

interface ValueFaker
{
    public function generateFakeValue(
        Generator $faker,
        ReflectionProperty $property,
    ): mixed;
}
