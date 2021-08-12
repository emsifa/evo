<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Emsifa\Evo\Rules\SizeRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Between extends SizeRule implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected int $min,
        protected int $max,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        $this->type = $this->getSizeType($value);

        return $this->validateBetween($attribute, $value, [$this->min, $this->max]);
    }

    public function message()
    {
        $params = ['min' => $this->min, 'max' => $this->max];

        return __($this->message, $params)
            ?: __("validation.between.{$this->type}", $params);
    }
}
