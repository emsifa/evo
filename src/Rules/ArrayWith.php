<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ArrayWith implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected array $keys,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateArray($attribute, $value, $this->keys);
    }

    public function message()
    {
        return __($this->message)
            ?: __('validation.array_with', ['keys' => implode(", ", $this->keys)])
            ?: __('validation.array');
    }
}
