<?php

namespace Emsifa\Evo\DTO;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class UseFaker
{
    protected array $args;

    public function __construct(
        protected string $fakerMethodName,
        mixed ...$args,
    )
    {
        $this->args = $args;
    }

    public function getFakerMethodName(): string
    {
        return $this->fakerMethodName;
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}
