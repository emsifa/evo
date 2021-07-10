<?php

namespace Emsifa\Evo\Tests\Samples\Http;

use Emsifa\Evo\Contracts\JsonData;
use Emsifa\Evo\Contracts\JsonTemplate;
use Emsifa\Evo\Http\Response\JsonResponse;

class SampleJsonTemplate implements JsonTemplate
{
    public int $status;
    public JsonData $data;

    /**
     * @param BaseResponse $response
     */
    public function forJsonResponse(JsonResponse $response): static
    {
        $this->status = $response->getStatus();
        $this->data = $response->getData();
        return $this;
    }
}
