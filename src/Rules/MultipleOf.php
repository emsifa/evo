<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MultipleOf implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected float | int $value,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateMultipleOf($attribute, $value, [$this->value]);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.multiple_of", ['value' => $this->value]);
    }
}
