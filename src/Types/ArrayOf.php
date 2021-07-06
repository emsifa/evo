<?php

namespace Emsifa\Evo\Types;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ArrayOf
{
    const THROW_ERROR = 0;
    const SKIP_ITEM = 1;
    const NULL_ITEM = 2;
    const KEEP_AS_IS = 3;

    public function __construct(
        protected string $type,
        protected int $ifCastError = 0,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getIfCastError(): int
    {
        return $this->ifCastError;
    }
}
