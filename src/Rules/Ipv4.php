<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Ipv4 implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected string $message = '',
    ) {}

    public function passes($attribute, $value)
    {
        return $this->validateIpv4($attribute, $value);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.ipv4");
    }
}
