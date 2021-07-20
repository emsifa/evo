<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SameWith implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string $field,
        protected string $message = '',
    ) {}

    public function passes($attribute, $value)
    {
        return $this->validateSame($attribute, $value, [$this->field]);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.same", ['other' => $this->field]);
    }
}
