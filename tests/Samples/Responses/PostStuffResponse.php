<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Types\ArrayOf;

class PostStuffResponse extends JsonResponse
{
    public int $id;
    public string $name;
    public string $stuff;
    public StuffRelation $relation;

    #[ArrayOf(StuffRelation::class)]
    public array $otherRelations;
}
