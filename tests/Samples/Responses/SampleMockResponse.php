<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Dto\FakesCount;
use Emsifa\Evo\Dto\UseFaker;
use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Tests\Samples\CategoryFaker;
use Emsifa\Evo\Types\ArrayOf;

class SampleMockResponse extends JsonResponse
{
    // Scalar Types
    public int $int;
    public float $float;
    public string $string;
    public bool $bool;

    // Filled with request
    public int $numberFromRequest;
    public string $nameFromRequest;

    // Existing faker method name
    public string $uuid;
    public string $creditCardNumber;

    // Scalar types with faker
    #[UseFaker("numberBetween", 1500, 1505)]
    public int $fakeInt;

    #[UseFaker("randomFloat", 2, 2000, 2001)]
    public int $fakeFloat;

    #[UseFaker("randomElement", ["foo", "bar", "baz"])]
    public string $fakeString;

    // Object
    public ChildMockData $child;

    // Array
    #[ArrayOf('int')]
    public array $numbers;

    #[ArrayOf(ChildMockData::class)]
    #[FakesCount(7)]
    public array $childs;

    #[UseFaker(CategoryFaker::class, type: "framework")]
    public string $category;

    public $mixed;
}
