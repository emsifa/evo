<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Distinct implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected bool $strict = false,
        protected bool $ignoreCase = false,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateDistinct($attribute, $value, $this->getParameters());
    }

    public function getParameters(): array
    {
        $params = [];
        if ($this->strict) {
            $params[] = 'strict';
        }
        if ($this->ignoreCase) {
            $params[] = 'ignore_case';
        }

        return $params;
    }

    public function message()
    {
        return __($this->message) ?: __('validation.distinct');
    }
}
