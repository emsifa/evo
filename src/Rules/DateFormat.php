<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DateFormat implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string $format,
        protected string $message = '',
    ) {}

    public function passes($attribute, $value)
    {
        return $this->validateDateFormat($attribute, $value, [$this->format]);
    }

    public function message()
    {
        return __($this->message) ?: __('validation.date_format', ['format' => $this->format]);
    }
}
