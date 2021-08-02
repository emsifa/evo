<?php

namespace Emsifa\Evo\Http\Response;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS + Attribute::TARGET_METHOD + Attribute::IS_REPEATABLE)]
class UseErrorResponse
{
    public function __construct(
        protected string $responseClassName,
        /**
         * @var string[]
         */
        protected array $exceptionClassNames = [],
    ) {
    }

    public function getResponseClassName(): string
    {
        return $this->responseClassName;
    }

    public function getExceptionClassNames(): array
    {
        return $this->exceptionClassNames;
    }
}
