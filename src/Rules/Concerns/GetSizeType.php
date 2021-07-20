<?php

namespace Emsifa\Evo\Rules\Concerns;

use Symfony\Component\HttpFoundation\File\File;

trait GetSizeType
{
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
