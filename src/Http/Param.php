<?php

namespace Emsifa\Evo\Http;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiParameter;
use Emsifa\Evo\Contracts\RequestGetter;
use Emsifa\Evo\Contracts\RequestValidator;
use Emsifa\Evo\Helpers\OpenAPIHelper;
use Emsifa\Evo\Swagger\OpenAPI\Schemas\Parameter;
use Illuminate\Http\Request;
use ReflectionParameter;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY + Attribute::TARGET_PARAMETER)]
class Param extends CommonGetterAndValidator implements RequestGetter, RequestValidator, OpenApiParameter
{
    public function getValue(Request $request, string $key): mixed
    {
        return $request->route($key);
    }

    public function getOpenApiParameter(ReflectionParameter|ReflectionProperty $reflection): Parameter
    {
        $name = $this->getKey($reflection);
        $in = Parameter::IN_PATH;

        return $reflection instanceof ReflectionParameter
            ? OpenAPIHelper::makeParameterFromParameter($reflection, $name, $in)
            : OpenAPIHelper::makeParameterFromProperty($reflection, $name, $in);
    }
}
