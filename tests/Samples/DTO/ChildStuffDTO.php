<?php

namespace Emsifa\Evo\Tests\Samples\Dto;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Swagger\OpenApi\Example;
use Emsifa\Evo\Types\ArrayOf;

class ChildStuffDto extends Dto
{
    #[Example('A thing')]
    public string $thing;

    #[ArrayOf('int')]
    #[Example([1, 2, 3])]
    public array $numbers;
}
