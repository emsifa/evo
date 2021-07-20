<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Emsifa\Evo\Rules\Concerns\GetSizeType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class InArray implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string $field,
        protected string $message = '',
    ) {}

    public function passes($attribute, $value)
    {
        return $this->validateInArray($attribute, $value, [$this->field]);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.in_array", ['other' => $this->field]);
    }
}
