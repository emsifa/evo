<?php

namespace Emsifa\Evo\Tests\Samples\Http;

class CreateUserResponse extends BaseResponse
{
    public int $id;
    public string $name;
    public string $email;
    public string $createdAt;
}
