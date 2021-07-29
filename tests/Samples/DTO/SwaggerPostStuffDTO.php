<?php

namespace Emsifa\Evo\Tests\Samples\DTO;

use Emsifa\Evo\DTO;
use Emsifa\Evo\Swagger\OpenApi\Description;

#[Description("Post stuff data")]
class SwaggerPostStuffDTO extends DTO
{
    public int $age;
    public string $name;
    public string $email;
    public ChildStuffDTO $child;
}
