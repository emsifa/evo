<?php

namespace Emsifa\Evo\Tests\Samples;

use Emsifa\Evo\Casters\IntCaster;
use Emsifa\Evo\Dto\CastWith;
use Emsifa\Evo\Dto\UseCaster;
use Emsifa\Evo\Tests\Samples\Casters\HalfIntCaster;

#[UseCaster('int', IntCaster::class)]
class ObjectWithOverridedCaster
{
    #[CastWith(HalfIntCaster::class)]
    public int $number;
}
