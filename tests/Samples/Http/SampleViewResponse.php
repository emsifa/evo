<?php

namespace Emsifa\Evo\Tests\Samples\Http;

use Emsifa\Evo\Http\Response\ViewResponse;

class SampleViewResponse extends ViewResponse
{
    protected string $viewName = "sample-view";

    public int $id;
    public string $name;
    public string $email;
    public string $createdAt;
}
