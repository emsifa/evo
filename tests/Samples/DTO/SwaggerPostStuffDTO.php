<?php

namespace Emsifa\Evo\Tests\Samples\DTO;

use Emsifa\Evo\DTO;
use Emsifa\Evo\Swagger\OpenApi\Description;
use Emsifa\Evo\Swagger\OpenApi\Example;

#[Description("Post stuff data")]
class SwaggerPostStuffDTO extends DTO
{
    #[Example(18)]
    public int $age;

    #[Example('John Doe')]
    public string $name;

    #[Example('johndoe@mail.com')]
    public string $email;

    public ChildStuffDTO $child;
}
