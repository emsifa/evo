<?php

namespace Emsifa\Evo\Contracts;

use Illuminate\Http\Request;
use ReflectionParameter;
use ReflectionProperty;

interface RequestValidator
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateRequest(Request $request, ReflectionProperty | ReflectionParameter $reflection);
}
