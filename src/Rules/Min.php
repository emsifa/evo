<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Min extends SizeRule implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected int $min,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        $this->type = $this->getSizeType($value);

        return $this->validateMin($attribute, $value, [$this->min]);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.min.{$this->type}", ['min' => $this->min]);
    }
}
