<?php

namespace Emsifa\Evo\Tests\Samples\Http;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\UseJsonTemplate;

#[UseJsonTemplate(SampleJsonTemplate::class)]
abstract class BaseResponse extends JsonResponse
{
    const STATUS_ERR = 0;
    const STATUS_OK = 1;

    protected int $status;

    public function withStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
