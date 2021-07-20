<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Emsifa\Evo\Rules\Concerns\GetSizeType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Max implements Rule
{
    use ValidatesAttributes;
    use GetSizeType;

    protected ?string $type = null;

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
        return __($this->message) ?: __("validation.between.{$this->type}", ['max' => $this->max]);
    }
}
