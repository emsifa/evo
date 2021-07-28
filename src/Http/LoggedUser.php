<?php

namespace Emsifa\Evo\Http;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiParameter;
use Emsifa\Evo\Contracts\RequestGetter;
use Emsifa\Evo\Contracts\RequestValidator;
use Emsifa\Evo\Helpers\OpenApiHelper;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ReflectionParameter;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY + Attribute::TARGET_PARAMETER)]
class LoggedUser implements RequestGetter
{
    public function __construct(protected ?string $guard = null)
    {
    }

    public function getRequestValue(Request $request, ReflectionParameter|ReflectionProperty $reflection): mixed
    {
        return $request->user($this->guard);
    }
}
