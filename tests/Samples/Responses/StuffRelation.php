<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Swagger\OpenApi\Example;

class StuffRelation extends JsonResponse
{
    #[Example('Relation thing')]
    public string $thing;
}
