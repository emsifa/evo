<?php

namespace Emsifa\Evo\Http\Middlewares;

use Closure;
use Emsifa\Evo\Http\Response\Mock;
use Illuminate\Contracts\Container\Container;

class AddMockHeader
{
    public function __construct(protected Container $container)
    {
    }

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $mock = $this->container->make(Mock::class);
        $response->headers->set("Evo-Mock", $mock->getClassName());

        return $response;
    }
}
