<?php

namespace Emsifa\Evo\Tests\Samples;

use Emsifa\Evo\Contracts\ValueFaker;
use Faker\Generator;
use ReflectionProperty;

class CategoryFaker implements ValueFaker
{
    public function __construct(protected string $type)
    {
    }

    public function generateFakeValue(Generator $faker, ReflectionProperty $property): mixed
    {
        if ($this->type == "framework") {
            return $faker->randomElement(["Laravel", "Express.js", "Nest.js"]);
        } else {
            return $faker->randomElement(["Dessert", "Appetizer", "Cocktail"]);
        }
    }
}
