<?php

namespace Emsifa\Evo\Route;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Delete extends Route
{
    /**
     * Create a new Route instance.
     *
     * @param  string       $uri
     * @param  string|array $middleware
     * @param  string       $domain
     * @param  array        $where
     * @return void
     */
    public function __construct(
        string $uri = '',
        $middleware = '',
        string $domain = '',
        array $where = [],
    ) {
        parent::__construct(['DELETE'], $uri, $middleware, $domain, $where);
    }
}
