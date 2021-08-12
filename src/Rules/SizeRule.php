<?php

namespace Emsifa\Evo\Rules;

use Symfony\Component\HttpFoundation\File\File;

abstract class SizeRule
{
    protected ?string $type = null;
    protected $numericRules = [];

    protected function hasRule($attribute, $rules)
    {
        return match ($this->type) {
            "numeric" => true,
            default => false,
        };
    }

    public function getSizeType($value)
    {
        if (is_numeric($value)) {
            return "numeric";
        }
        if (is_string($value)) {
            return "string";
        }
        if (is_array($value)) {
            return "array";
        }
        if ($value instanceof File) {
            return "file";
        }

        return null;
    }
}
