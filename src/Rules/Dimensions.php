<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Dimensions implements Rule
{
    use ValidatesAttributes;

    public function __construct(
        protected ?int $min_width = null,
        protected ?int $max_width = null,
        protected ?int $min_height = null,
        protected ?int $max_height = null,
        protected ?int $width = null,
        protected ?int $height = null,
        protected ?float $ratio = null,
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateDimensions($attribute, $value, $this->getParameters());
    }

    public function getParameters(): array
    {
        $params = [
            'min_width' => $this->min_width,
            'max_width' => $this->max_width,
            'min_height' => $this->min_height,
            'max_height' => $this->max_height,
            'width' => $this->width,
            'height' => $this->height,
            'ratio' => $this->ratio,
        ];

        return collect($params)
            ->filter(fn ($value) => ! is_null($value))
            ->map(fn ($value, $key) => "{$key}={$value}")
            ->values()
            ->toArray();
    }

    public function message()
    {
        return __($this->message) ?: __('validation.dimensions');
    }
}
