<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;

class PostStuffResponse extends JsonResponse
{
    public int $id;
    public string $name;
    public string $stuff;
}
