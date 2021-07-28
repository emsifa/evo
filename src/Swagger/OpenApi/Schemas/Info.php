<?php

namespace Emsifa\Evo\Swagger\OpenApi\Schemas;

class Info extends BaseSchema
{
    public string $title;
    public string $version;
    public ?string $description = null;
    public ?string $termsOfService = null;
    public ?Contact $contact = null;
    public ?License $license = null;
}
