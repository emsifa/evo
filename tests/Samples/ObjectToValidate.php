<?php

namespace Emsifa\Evo\Tests\Samples;

use Emsifa\Evo\Rules\Exists;
use Emsifa\Evo\Rules\Required;
use Emsifa\Evo\Rules\SameWith;
use Emsifa\Evo\Types\ArrayOf;

class ChildObjectToValidate
{
    public int $id;
    public string $name;
}

class ObjectToValidate
{
    public int $myInt;
    public float $myFloat;
    public bool $myBool;
    public string $myString;
    public array $myMixedArray;

    #[ArrayOf('int')]
    public array $myArrayOfInt;

    #[ArrayOf(ChildObjectToValidate::class)]
    public array $childs;

    #[Required]
    #[SameWith('foo')]
    #[Exists('foo', 'bar')]
    public string $attrRules;
}
