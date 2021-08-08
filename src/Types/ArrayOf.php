<?php

namespace Emsifa\Evo\Types;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiSchemaModifier;
use Emsifa\Evo\Helpers\OpenApiHelper;
use Emsifa\Evo\Helpers\TypeHelper;
use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseDto;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Schema;
use ReflectionClass;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ArrayOf implements OpenApiSchemaModifier
{
    const THROW_ERROR = 0;
    const SKIP_ITEM = 1;
    const NULL_ITEM = 2;
    const KEEP_AS_IS = 3;

    public function __construct(
        protected string $type,
        protected int $ifCastError = 0,
        protected bool $associative = false,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getIfCastError(): int
    {
        return $this->ifCastError;
    }

    public function modifyOpenApiSchema(Schema $schema)
    {
        $isResponse = ! TypeHelper::isBuiltInType($this->type)
            && (is_subclass_of($this->type, JsonResponse::class) || is_subclass_of($this->type, ResponseDto::class));

        $itemsSchema = TypeHelper::isBuiltInType($this->type)
            ? new Schema(type: OpenApiHelper::getType($this->type))
            : OpenApiHelper::makeSchemaFromClass(new ReflectionClass($this->type), ! $isResponse);

        if ($this->associative) {
            $schema->type = Schema::TYPE_OBJECT;
            $schema->additionalProperties = $itemsSchema;
        } else {
            $schema->type = Schema::TYPE_ARRAY;
            $schema->items = $itemsSchema;
        }
    }
}
