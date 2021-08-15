<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Emsifa\Evo\Rules\Concerns\SizeUtilities;
use Illuminate\Contracts\Validation\Rule;

#[Attribute(Attribute::TARGET_PROPERTY)]
class GreaterThan extends RuleWithData implements Rule
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

        return $this->getSize($value) > $this->getSize($other);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.gt.{$this->type}", ['value' => $this->field]);
    }
}
