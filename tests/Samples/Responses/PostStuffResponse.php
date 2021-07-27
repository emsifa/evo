<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\UseJsonTemplate;
use Emsifa\Evo\Tests\Samples\Http\SampleJsonTemplate;
use Emsifa\Evo\Types\ArrayOf;

#[UseJsonTemplate(SampleJsonTemplate::class)]
class PostStuffResponse extends JsonResponse
{
    public int $id;
    public string $name;
    public string $stuff;
    public StuffRelation $relation;

    #[ArrayOf(StuffRelation::class)]
    public array $otherRelations;
}
