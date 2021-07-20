<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MimeTypes implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        /**
         * @var string|string[]
         */
        protected string | array $mimeTypes,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateMimetypes($attribute, $value, $this->getParameters());
    }

    public function getParameters(): array
    {
        if (is_string($this->mimeTypes)) {
            return explode($this->mimeTypes, ",");
        }

        return $this->mimeTypes;
    }

    public function message()
    {
        return __($this->message) ?: __("validation.mimetypes", ['values' => implode(", ", $this->getParameters())]);
    }
}
