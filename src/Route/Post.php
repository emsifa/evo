<?php

namespace Emsifa\Evo\Route;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Post extends Route
{
    /**
     * Create a new Route instance.
     *
     * @param  string       $uri
     * @param  string|array $middleware
     * @param  string|null  $name
     * @param  string|null  $domain
     * @param  array        $where
     * @return void
     */
    public function __construct(
        string $uri = '',
        $middleware = '',
        ?string $name = null,
        ?string $domain = null,
        array $where = [],
    ) {
        parent::__construct(['POST'], $uri, $middleware, $name, $domain, $where);
    }
}
