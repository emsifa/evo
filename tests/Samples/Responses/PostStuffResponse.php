<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\UseJsonTemplate;
use Emsifa\Evo\Swagger\OpenApi\Description;
use Emsifa\Evo\Swagger\OpenApi\Example;
use Emsifa\Evo\Tests\Samples\Http\SampleJsonTemplate;
use Emsifa\Evo\Types\ArrayOf;

#[UseJsonTemplate(SampleJsonTemplate::class)]
#[Description('Post stuff success response')]
class PostStuffResponse extends JsonResponse
{
    #[Example(1)]
    public int $id;

    #[Example('Lorem Ipsum')]
    public string $name;

    #[Example('A stuff')]
    public string $stuff;

    public StuffRelation $relation;

    #[ArrayOf(StuffRelation::class)]
    public array $otherRelations;

    #[ArrayOf(StuffRelation::class, associative: true)]
    public array $assoc;
}
