<?php

namespace Emsifa\Evo\Rules\Concerns;

use Symfony\Component\HttpFoundation\File\File;

trait SizeUtilities
{
    public function getSize($value)
    {
        return match ($this->getSizeType($value)) {
            "numeric" => $value,
            "string" => strlen($value),
            "array" => count($value),
            "file" => $value->getSize() / 1024,
            default => 0,
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
