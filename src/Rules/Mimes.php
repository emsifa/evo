<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Mimes implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        /**
         * @var string[] $mimes
         */
        protected array $mimes,
        protected string $message = '',
    ) {}

    /**
     * @param  string $attribute
     * @param  \Illuminate\Http\UploadedFile $value
     */
    public function passes($attribute, $value)
    {
        return $this->validateMimes($attribute, $value, $this->mimes);
    }

    public function message()
    {
        return __($this->message) ?: __('validation.mimes', ['values' => implode(", ", $this->mimes)]);
    }
}
