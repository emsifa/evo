<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class Schema extends BaseSchema
{
    const TYPE_INTEGER = "integer";
    const TYPE_NUMBER = "number";
    const TYPE_STRING = "string";
    const TYPE_BOOLEAN = "boolean";
    const TYPE_ARRAY = "array";
    const TYPE_OBJECT = "object";

    const FORMAT_INT32 = "int32";
    const FORMAT_INT64 = "int64";
    const FORMAT_FLOAT = "float";
    const FORMAT_DOUBLE = "double";
    const FORMAT_BYTE = "byte";
    const FORMAT_BINARY = "binary";
    const FORMAT_DATE = "date";
    const FORMAT_DATETIME = "date-time";
    const FORMAT_PASSWORD = "password";

    public function __construct(
        public string $type,
        public ?string $format = null,
        public ?string $description = null,
        public mixed $default = null,
        public ?bool $nullable = null,
        public mixed $example = null,
        public ?bool $deprecated = null,

        /**
         * @var string[]|null
         */
        public ?array $required = null,

        /**
         * @var PropertySchema[]|null
         */
        public ?array $properties = null,
        public Schema | Reference | null $items = null,
        public ?float $minimum = null,
        public ?float $maximum = null,
    ) {
    }
}
