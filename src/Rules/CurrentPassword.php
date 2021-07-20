<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CurrentPassword implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string $guard,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateCurrentPassword($attribute, $value, [$this->guard]);
    }

    public function message()
    {
        return __($this->message) ?: __('validation.current_password');
    }
}
