<?php

namespace Emsifa\Evo\Tests\Samples\Dto;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Swagger\OpenApi\Description;
use Emsifa\Evo\Swagger\OpenApi\Example;

#[Description("Post stuff data")]
class SwaggerPostStuffDto extends Dto
{
    #[Example(18)]
    public int $age;

    #[Example('John Doe')]
    public string $name;

    #[Example('johndoe@mail.com')]
    public string $email;

    public ChildStuffDto $child;
}
