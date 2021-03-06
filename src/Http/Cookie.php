<?php

namespace Emsifa\Evo\Http;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiParameter;
use Emsifa\Evo\Contracts\RequestGetter;
use Emsifa\Evo\Contracts\RequestValidator;
use Emsifa\Evo\Helpers\OpenApiHelper;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;
use Illuminate\Http\Request;
use ReflectionParameter;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY + Attribute::TARGET_PARAMETER)]
class Cookie extends CommonGetterAndValidator implements RequestGetter, RequestValidator, OpenApiParameter
{
    public function hasValue(Request $request, string $key): mixed
    {
        return $request->cookies->has($key);
    }

    public function getValue(Request $request, string $key): mixed
    {
        return $request->cookie($key);
    }

    public function getOpenApiParameter(ReflectionParameter | ReflectionProperty $reflection): Parameter
    {
        $name = $this->getKey($reflection);
        $in = Parameter::IN_COOKIE;

        return $reflection instanceof ReflectionParameter
            ? OpenApiHelper::makeParameterFromParameter($reflection, $name, $in)
            : OpenApiHelper::makeParameterFromProperty($reflection, $name, $in);
    }
}
