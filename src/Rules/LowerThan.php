<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Emsifa\Evo\Rules\Concerns\GetSizeType;
use Emsifa\Evo\Rules\Concerns\SizeUtilities;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class LowerThan extends RuleWithData implements Rule
{
    use SizeUtilities;

    protected string $type;

    public function __construct(
        protected string $field,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        $this->type = $this->getSizeType($value);

        $other = $this->getValue($this->field);

        $valueSize = $this->getSize($value);
        $otherSize = $this->getSize($other);

        return $valueSize < $otherSize;
    }

    public function message()
    {
        return __($this->message) ?: __("validation.lt.{$this->type}", ['value' => $this->field]);
    }
}
