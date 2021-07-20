<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EndsWith implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        /**
         * @var string[]|string
         */
        protected $values,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateEndsWith($attribute, $value, $this->getParameters());
    }

    public function getParameters(): array
    {
        if (is_string($this->values)) {
            return explode(",", $this->values);
        }

        return $this->values;
    }

    public function message()
    {
        return __($this->message) ?: __('validation.ends_with', ['values' => implode(", ", $this->getParameters())]);
    }
}
