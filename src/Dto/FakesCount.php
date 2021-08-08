<?php

namespace Emsifa\Evo\Dto;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FakesCount
{
    public function __construct(
        protected int $minOrCount,
        protected ?int $max = null,
    ) {
    }

    public function getCount(): int
    {
        if ($this->max) {
            return rand($this->minOrCount, $this->max);
        }

        return $this->minOrCount;
    }
}
