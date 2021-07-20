<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Emsifa\Evo\Rules\Concerns\GetSizeType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Size implements Rule
{
    use ValidatesAttributes;
    use GetSizeType;

    protected string $type;

    public function __construct(
        protected int|float $size,
        protected string $message = '',
    ) {}

    public function passes($attribute, $value)
    {
        $this->type = $this->getSizeType($value);
        return $this->validateSize($attribute, $value, [$this->size]);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.size.{$this->type}", ['size' => $this->size]);
    }
}
