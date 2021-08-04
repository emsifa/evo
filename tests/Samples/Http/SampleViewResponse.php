<?php

namespace Emsifa\Evo\Tests\Samples\Http;

use Emsifa\Evo\Http\Response\UseView;
use Emsifa\Evo\Http\Response\ViewResponse;

#[UseView('sample-view')]
class SampleViewResponse extends ViewResponse
{
    public int $id;
    public string $name;
    public string $email;
    public string $createdAt;
}
