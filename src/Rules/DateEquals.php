<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DateEquals extends DateRule implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string $date,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateDateEquals($attribute, $value, [$this->date]);
    }

    public function message()
    {
        return __($this->message) ?: __('validation.date_equals', ['date' => date_create($this->date)->format('Y-m-d H:i:s')]);
    }
}
