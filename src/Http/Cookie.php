<?php

namespace Emsifa\Evo\Http;

use Attribute;
use Emsifa\Evo\Contracts\RequestGetter;
use Emsifa\Evo\Contracts\RequestValidator;
use Illuminate\Http\Request;

#[Attribute(Attribute::TARGET_PROPERTY + Attribute::TARGET_PARAMETER)]
class Cookie extends CommonGetterAndValidator implements RequestGetter, RequestValidator
{
    public function getValue(Request $request, string $key): mixed
    {
        return $request->cookie($key);
    }
}
