<?php

namespace Emsifa\Evo\Route;

use Attribute;
use Emsifa\Evo\ControllerDispatcher;
use Illuminate\Routing\Route as BaseRoute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route extends BaseRoute
{
    /**
     * Create a new Route instance.
     *
     * @param  string|string[]      $methods
     * @param  string               $uri
     * @param  string               $name
     * @param  string|array|null    $middleware
     * @param  string|null          $domain
     * @param  array                $where
     * @return void
     */
    public function __construct(
        $methods,
        string $uri,
        $middleware = null,
        ?string $name = null,
        ?string $domain = null,
        array $where = [],
    ) {
        $this->uri = $uri;
        $this->methods = (array) $methods;

        if (in_array('GET', $this->methods) && ! in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }

        if ($middleware) {
            $this->action['middleware'] = $middleware;
        }

        if ($domain) {
            $this->action['domain'] = $domain;
        }

        if ($name) {
            $this->name($name);
        }

        foreach ($where as $key => $cond) {
            $this->where($key, $cond);
        }
    }

    public function setUses($controller)
    {
        $this->action['uses'] = $controller;
        $this->action['controller'] = $controller;
    }

    /**
     * Get the dispatcher for the route's controller.
     *
     * @return \Illuminate\Routing\Contracts\ControllerDispatcher
     */
    public function controllerDispatcher()
    {
        if ($this->container->bound(ControllerDispatcherContract::class)) {
            return $this->container->make(ControllerDispatcherContract::class);
        }

        return new ControllerDispatcher($this->container);
    }

    public function toArray()
    {
        $array = [];
        if ($this->domain) {
            $array['domain'] = $this->domain;
        }
        if ($this->where) {
            $array['where'] = $this->where;
        }
        if ($this->name) {
            $array['as'] = $this->name;
        }

        return $array;
    }
}
