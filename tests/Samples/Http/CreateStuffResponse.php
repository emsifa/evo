<?php

namespace Emsifa\Evo\Tests\Samples\Http;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\UseJsonTemplate;

#[UseJsonTemplate(JsonTemplateWithStatus::class, status: 201)]
class CreateStuffResponse extends JsonResponse
{
    public int $id;
    public string $stuff;
}
