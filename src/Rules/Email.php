<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Email implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected bool $strict = false,
        protected bool $rfc = false,
        protected bool $dns = false,
        protected bool $spoof = false,
        protected bool $filter = false,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateEmail($attribute, $value, $this->getParameters());
    }

    public function getParameters(): array
    {
        $params = [
            $this->strict ? 'strict' : null,
            $this->rfc ? 'rfc' : null,
            $this->dns ? 'dns' : null,
            $this->spoof ? 'spoof' : null,
            $this->filter ? 'filter' : null,
        ];

        return collect($params)->filter(fn ($value) => ! is_null($value))->toArray();
    }

    public function message()
    {
        return __($this->message) ?: __('validation.email');
    }
}
