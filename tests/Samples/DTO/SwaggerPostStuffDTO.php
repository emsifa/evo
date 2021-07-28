<?php

namespace Emsifa\Evo\Tests\Samples\DTO;

use Emsifa\Evo\DTO;

class SwaggerPostStuffDTO extends DTO
{
    public int $age;
    public string $name;
    public string $email;
    public ChildStuffDTO $child;
}
