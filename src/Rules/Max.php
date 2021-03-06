<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Max extends SizeRule implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected int $max,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        $this->type = $this->getSizeType($value);

        return $this->validateMax($attribute, $value, [$this->max]);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.max.{$this->type}", ['max' => $this->max]);
    }
}
