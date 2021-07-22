<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ExcludeIf implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string $field,
        protected mixed $value,
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateExcludeIf($attribute, $value, [$this->field, $this->value]);
    }

    public function message()
    {
        return "";
    }
}
