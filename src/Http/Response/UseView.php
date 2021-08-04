<?php

namespace Emsifa\Evo\Http\Response;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseView
{
    public function __construct(protected string $viewName)
    {
    }

    public function getViewName(): string
    {
        return $this->viewName;
    }
}
