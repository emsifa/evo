<?php

namespace Emsifa\Evo\Contracts;

use Faker\Generator;
use Illuminate\Http\Request;

interface Mockable
{
    public function getMockedData(Generator $faker, Request $request): array;
}
