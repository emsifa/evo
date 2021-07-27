<?php

namespace Emsifa\Evo\Tests\Samples\DTO;

use Emsifa\Evo\DTO;
use Emsifa\Evo\Types\ArrayOf;

class ChildStuffDTO extends DTO
{
    public string $thing;

    #[ArrayOf('int')]
    public array $numbers;
}
