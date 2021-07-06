<?php

namespace Emsifa\Evo\DTO;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CastWith
{
    public function __construct(
        protected string $caster,
    ){}

    public function getCaster(): string
    {
        return $this->caster;
    }
}
