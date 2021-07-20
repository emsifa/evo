<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class AlphaDash implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateAlphaDash($attribute, $value);
    }

    public function message()
    {
        return __($this->message) ?: __('validation.alpha_dash');
    }
}
