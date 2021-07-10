<?php

namespace Emsifa\Evo;

use DateTime;
use Emsifa\Evo\Casters\ArrayCaster;
use Emsifa\Evo\Casters\BoolCaster;
use Emsifa\Evo\Casters\CollectionCaster;
use Emsifa\Evo\Casters\DateTimeCaster;
use Emsifa\Evo\Casters\FloatCaster;
use Emsifa\Evo\Casters\IntCaster;
use Emsifa\Evo\Casters\StringCaster;
use Emsifa\Evo\DTO\UseCaster;
use Emsifa\Evo\Helpers\ObjectHelper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

#[UseCaster('int', IntCaster::class)]
#[UseCaster('float', FloatCaster::class)]
#[UseCaster('string', StringCaster::class)]
#[UseCaster('bool', BoolCaster::class)]
#[UseCaster('array', ArrayCaster::class)]
#[UseCaster(DateTime::class, DateTimeCaster::class)]
#[UseCaster(Collection::class, CollectionCaster::class)]
abstract class DTO implements Arrayable
{
    public function toArray(): array
    {
        return ObjectHelper::toArray($this);
    }

    public static function fromArray(array $data): static
    {
        $object = new static;

        ObjectFiller::fillObject($object, $data);

        return $object;
    }

    public static function fromRequest(Request $request): static
    {
        $inputs = $request->all();
        $files = Arr::dot($request->allFiles());
        foreach ($files as $key => $file) {
            Arr::set($inputs, $key, $file);
        }

        return static::fromArray($inputs);
    }
}
