<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Exists implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string $table,
        protected string $column,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateExists($attribute, $value, [$this->table, $this->column]);
    }

    public function message()
    {
        return __($this->message) ?: __('validation.exists');
    }
}
