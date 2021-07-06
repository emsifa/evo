<?php

namespace Emsifa\Evo\DTO;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseCaster
{
    public function __construct(
        protected string $type,
        protected string $caster,
    ){}

    public function getType(): string
    {
        return $this->type;
    }

    public function getCaster(): string
    {
        return $this->caster;
    }
}
