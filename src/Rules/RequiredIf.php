<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class RequiredIf implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string $field,
        protected mixed $values,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateRequiredIf($attribute, $value, $this->getParameters());
    }

    protected function getParameters()
    {
        $values = is_array($this->values) ? $this->values : explode(",", $this->values);

        return [$this->field, ...$values];
    }

    public function message()
    {
        return __($this->message) ?: __("validation.required_if", ['other' => $this->field, 'value' => $this->stringValues()]);
    }

    protected function stringValues()
    {
        return is_array($this->values) ? implode(", ", $this->values) : $this->values;
    }
}
