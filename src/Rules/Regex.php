<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Regex implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string $pattern,
        protected string $message = '',
    ) {}

    public function passes($attribute, $value)
    {
        return $this->validateRegex($attribute, $value, [$this->pattern]);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.regex");
    }
}
