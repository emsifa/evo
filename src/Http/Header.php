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
class Header extends CommonGetterAndValidator implements RequestGetter, RequestValidator, OpenApiParameter
{
    public function hasValue(Request $request, string $key): mixed
    {
        return $request->headers->has($key);
    }

    public function getValue(Request $request, string $key): mixed
    {
        return $request->header(Str::snake($key, '-'));
    }

    public function getOpenApiParameter(ReflectionParameter | ReflectionProperty $reflection): Parameter
    {
        $name = $this->getKey($reflection);
        $in = Parameter::IN_HEADER;

        return $reflection instanceof ReflectionParameter
            ? OpenApiHelper::makeParameterFromParameter($reflection, $name, $in)
            : OpenApiHelper::makeParameterFromProperty($reflection, $name, $in);
    }
}
