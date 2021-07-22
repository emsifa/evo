<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class RequiredWith implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string | array $field,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateRequiredWith($attribute, $value, $this->getParameters());
    }

    protected function getParameters()
    {
        return is_array($this->field) ? $this->field : explode(",", $this->field);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.required_with", ['other' => $this->field, 'values' => implode(", ", $this->getParameters())]);
    }
}
