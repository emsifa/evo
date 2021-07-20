<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Emsifa\Evo\Rules\Concerns\GetSizeType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class LowerThanOrEqual implements Rule
{
    use ValidatesAttributes;
    use GetSizeType;

    protected string $type;

    public function __construct(
        protected string $field,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        $this->type = $this->getSizeType($value);

        return $this->validateLte($attribute, $value, [$this->field]);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.lte.{$this->type}", ['value' => $this->field]);
    }
}
