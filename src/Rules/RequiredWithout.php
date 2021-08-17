<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class RequiredWithout extends RuleWithData implements Rule, ImplicitRule
{
    use ValidatesAttributes;

    public function __construct(
        protected string | array $field,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateRequiredWithout($attribute, $value, $this->getParameters());
    }

    protected function getParameters()
    {
        return is_array($this->field) ? $this->field : explode(",", $this->field);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.required_without", ['values' => implode(", ", $this->getParameters())]);
    }
}
