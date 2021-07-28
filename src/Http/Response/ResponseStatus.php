<?php

namespace Emsifa\Evo\Http\Response;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ResponseStatus
{
    public function __construct(protected int $status)
    {
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
