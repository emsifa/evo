<?php

namespace Emsifa\Evo\Tests\Samples;

use DateTime;
use Emsifa\Evo\Casters\IntCaster;
use Emsifa\Evo\Dto\UseCaster;

#[UseCaster('int', IntCaster::class)]
class SimpleObjectWithIntCaster
{
    public int $integer;
    public float $float;
    public string $string;
    public bool $boolean;
    public array $array;
    public ?float $nullableFloat;
    public DateTime $date;
    public $mixed;
}
