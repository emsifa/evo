<?php

namespace Emsifa\Evo\Contracts;

use Emsifa\Evo\Http\Response\JsonResponse;

interface JsonTemplate
{
    /**
     * @param JsonResponse $response
     */
    public function forJsonResponse(JsonResponse $response): static;
}
