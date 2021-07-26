<?php

namespace Emsifa\Evo\Http\Response;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ResponseType
{
    public function __construct(protected string $type)
    {
    }

    public function getType(): string
    {
        return $this->type;
    }
}
