<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Contracts\Mockable;
use Emsifa\Evo\Http\Response\JsonResponse;
use Faker\Generator;
use Illuminate\Http\Request;

class SampleMockableResponse extends JsonResponse implements Mockable
{
    public int $int;
    public float $float;
    public string $string;
    public bool $bool;

    public function getMockedData(Generator $faker, Request $request): array
    {
        return [
            'int' => $faker->numberBetween(1000, 1005),
            'float' => $faker->randomFloat(2000, 2005),
            'string' => $faker->randomElement(["foo", "bar", "baz"]),
            'bool' => true,
        ];
    }
}
