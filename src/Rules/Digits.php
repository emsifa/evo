<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Digits implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected int $count,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateDigits($attribute, $value, [$this->count]);
    }

    public function message()
    {
        return __($this->message) ?: __('validation.digits', ['digits' => $this->count]);
    }
}
