<?php

namespace Emsifa\Evo\Rules;

use Emsifa\Evo\ValidationData;

abstract class RuleWithData
{
    protected ValidationData $data;

    public function setData(ValidationData $data)
    {
        $this->data = $data;
    }
}
