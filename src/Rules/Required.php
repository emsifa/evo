<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Required implements Rule, ImplicitRule
{
    use ValidatesAttributes;

    protected string $message;

    public function __construct(string $message = '')
    {
        $this->message = $message;
    }

    public function passes($attribute, $value)
    {
        return $this->validateRequired($attribute, $value);
    }

    public function message()
    {
        return __($this->message) ?: __('validation.required');
    }
}
