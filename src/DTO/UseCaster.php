<?php

namespace Emsifa\Evo\Dto;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class UseCaster
{
    public function __construct(
        protected string $type,
        protected string $caster,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCaster(): string
    {
        return $this->caster;
    }
}
