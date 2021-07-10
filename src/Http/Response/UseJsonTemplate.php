<?php

namespace Emsifa\Evo\Http\Response;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseJsonTemplate
{
    public function __construct(protected string $templateClassName)
    {
    }

    public function getTemplateClassName(): string
    {
        return $this->templateClassName;
    }
}
