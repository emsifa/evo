<?php

namespace Emsifa\Evo\Swagger\OpenAPI\Schemas;

class Info extends BaseSchema
{
    public string $title;
    public string $version;
    public ?string $description;
    public ?string $termsOfService;
    public ?Contact $contact;
    public ?License $license;
}
