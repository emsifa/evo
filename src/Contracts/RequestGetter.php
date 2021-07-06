<?php

namespace Emsifa\Evo\Contracts;

use Illuminate\Http\Request;
use ReflectionParameter;
use ReflectionProperty;

interface RequestGetter
{
    public function getRequestValue(Request $request, ReflectionParameter | ReflectionProperty $reflection): mixed;
}
