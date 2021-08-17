<?php

namespace Emsifa\Evo\Rules;

use Emsifa\Evo\ValidationData;
use Illuminate\Support\Arr;

abstract class RuleWithData
{
    protected ValidationData | array $data = [];

    public function setData(ValidationData $data)
    {
        $this->data = $data;
    }

    public function getValue($key)
    {
        return Arr::get($this->data, $key);
    }
}
