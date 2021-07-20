<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DigitsBetween implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected int $min,
        protected int $max,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateDigits($attribute, $value, [$this->min, $this->max]);
    }

    public function message()
    {
        return __($this->message) ?: __('validation.digits', ['min' => $this->min, 'max' => $this->max]);
    }
}
