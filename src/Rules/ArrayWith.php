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
        $fallback = trans()->has('validation.array_with') ? 'validation.array_with' : 'validation.array';

        return __($this->message) ?: __($fallback, ['keys' => implode(", ", $this->keys)]);
    }
}
