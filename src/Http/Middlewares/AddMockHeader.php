<?php

namespace Emsifa\Evo\Http\Middlewares;

use Closure;
use Emsifa\Evo\Http\Response\Mock;

class AddMockHeader
{
    public function __construct(protected Mock $mock)
    {
    }

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set("Evo-Mock", $this->mock->getClassName());

        return $response;
    }
}
