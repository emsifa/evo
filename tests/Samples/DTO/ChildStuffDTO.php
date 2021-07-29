<?php

namespace Emsifa\Evo\Tests\Samples\DTO;

use Emsifa\Evo\DTO;
use Emsifa\Evo\Swagger\OpenApi\Example;
use Emsifa\Evo\Types\ArrayOf;

class ChildStuffDTO extends DTO
{
    #[Example('A thing')]
    public string $thing;

    #[ArrayOf('int')]
    #[Example([1, 2, 3])]
    public array $numbers;
}
