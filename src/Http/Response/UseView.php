<?php

namespace Emsifa\Evo\Http\Response;

use Attribute;
use Emsifa\Evo\Contracts\JsonData;
use Emsifa\Evo\Contracts\OpenApiSchemaModifier;
use Emsifa\Evo\Helpers\OpenApiHelper;
use Emsifa\Evo\Helpers\TypeHelper;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;
use ReflectionClass;
use ReflectionProperty;

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
