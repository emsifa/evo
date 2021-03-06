<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class NotIn implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected array $values,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateNotIn($attribute, $value, $this->values);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.not_in");
    }
}
