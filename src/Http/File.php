<?php

namespace Emsifa\Evo\Http;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiRequestBodyModifier;
use Emsifa\Evo\Contracts\RequestGetter;
use Emsifa\Evo\Contracts\RequestValidator;
use Emsifa\Evo\Swagger\OpenApi\Schemas\RequestBody;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

#[Attribute(Attribute::TARGET_PROPERTY + Attribute::TARGET_PARAMETER)]
class File extends CommonGetterAndValidator implements RequestGetter, RequestValidator, OpenApiRequestBodyModifier
{
    public function getValue(Request $request, string $key): mixed
    {
        return $request->file($key);
    }

    public function modifyOpenApiRequestBody(RequestBody $body)
    {
        $schema = new Schema("string", format:"binary");
        $key = $this->key;
        if (! $body->content) {
            $contentSchema = new Schema("object", properties: [$key => $schema]);
            $body->content = ["multipart/form-data" => $contentSchema];
        } else {
            /**
             * @var Schema
             */
            $contentSchema = Arr::first($body->content);
            $contentSchema->properties[$key] = $schema;
            $body->content = ["multipart/form-data" => $contentSchema];
        }
    }
}
