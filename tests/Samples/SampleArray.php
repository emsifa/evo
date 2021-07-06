<?php

namespace Emsifa\Evo\Tests\Samples;

use Emsifa\Evo\Casters\IntCaster;
use Emsifa\Evo\DTO\UseCaster;
use Emsifa\Evo\Tests\Samples\ChildObject as SamplesChildObject;
use Emsifa\Evo\Types\ArrayOf;

class SampleArrayChildObject {
    public function __construct(
        public int $id = 0,
        public string $name = '',
    ) {}
}

#[UseCaster('int', IntCaster::class)]
class SampleArray
{
    public array $mixedArray;

    #[ArrayOf('int')]
    public array $arrayOfInt;

    #[ArrayOf('int', ifCastError: ArrayOf::THROW_ERROR)]
    public array $arrayOfIntThrownError;

    #[ArrayOf('int', ifCastError: ArrayOf::SKIP_ITEM)]
    public array $arrayOfIntSkipError;

    #[ArrayOf('int', ifCastError: ArrayOf::NULL_ITEM)]
    public array $arrayOfIntNullOnError;

    #[ArrayOf('int', ifCastError: ArrayOf::KEEP_AS_IS)]
    public array $arrayOfIntKeepAsIs;

    #[ArrayOf(SampleArrayChildObject::class)]
    public array $childs;
}
