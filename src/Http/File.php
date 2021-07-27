<?php

namespace Emsifa\Evo\Http;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiRequestBodyModifier;
use Emsifa\Evo\Contracts\RequestGetter;
use Emsifa\Evo\Contracts\RequestValidator;
use Emsifa\Evo\Swagger\OpenApi\Schemas\MediaType;
use Emsifa\Evo\Swagger\OpenApi\Schemas\RequestBody;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use ReflectionParameter;

#[Attribute(Attribute::TARGET_PROPERTY + Attribute::TARGET_PARAMETER)]
class File extends CommonGetterAndValidator implements RequestGetter, RequestValidator, OpenApiRequestBodyModifier
{
    public function getValue(Request $request, string $key): mixed
    {
        return $request->file($key);
    }

    public function modifyOpenApiRequestBody(RequestBody $body, mixed $reflection = null)
    {
        $schema = new Schema("string", format:"binary");
        $key = $this->key
            ? $this->key
            : ($reflection instanceof ReflectionParameter ? $reflection->getName() : "");

        if (! $body->content) {
            $contentSchema = new Schema(type: "object", properties: [$key => $schema]);

            if ($reflection && $reflection instanceof ReflectionParameter) {
                $refName = implode(".", [
                    $reflection->getDeclaringClass()->getName(),
                    $reflection->getDeclaringFunction()->getName(),
                ]);
                $contentSchema->setClassNameReference($refName);
            }

            $body->content = ["multipart/form-data" => new MediaType(schema: $contentSchema)];
        } else {
            /**
             * @var MediaType
             */
            $mediaType = Arr::first($body->content);
            $mediaType->schema->properties[$key] = $schema;

            if ($reflection && $reflection instanceof ReflectionParameter) {
                $refName = implode(".", [
                    $reflection->getDeclaringClass()->getName(),
                    $reflection->getDeclaringFunction()->getName(),
                ]);
                $mediaType->schema->setClassNameReference($refName);
            }

            $body->content = ["multipart/form-data" => $mediaType];
        }
    }
}
