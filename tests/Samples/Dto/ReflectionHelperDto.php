<?php

namespace Emsifa\Evo\Tests\Samples\Dto;

use Emsifa\Evo\Rules\Required;

class ReflectionHelperDto
{
    #[Required]
    public string $thing;

    public int | float $number;
}
