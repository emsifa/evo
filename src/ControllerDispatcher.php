<?php

namespace Emsifa\Evo;

use Emsifa\Evo\Contracts\RequestGetter;
use Emsifa\Evo\Contracts\RequestValidator;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Http\Response\Mock;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerDispatcher as BaseControllerDispatcher;
use Illuminate\Routing\Route;
use ReflectionAttribute;
use ReflectionMethod;
use ReflectionParameter;

class ControllerDispatcher extends BaseControllerDispatcher
{
    /**
     * Dispatch a request to a given controller and method.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  mixed  $controller
     * @param  string  $method
     * @return mixed
     */
    public function dispatch(Route $route, $controller, $method)
    {
        $request = $this->getRequest();

        $parameters = $this->resolveParameters($request, $controller, $method);

        $methodReflection = new ReflectionMethod($controller, $method);
        /**
         * @var Mock $mock
         */
        $mock = ReflectionHelper::getFirstAttributeInstance($methodReflection, Mock::class, ReflectionAttribute::IS_INSTANCEOF);
        if ($mock && $this->shouldReturnMock($mock, $request)) {
            return $mock->getMockedResponse($this->container, $methodReflection, $request);
        }

        return call_user_func_array([$controller, $method], $parameters);
    }

    public function shouldReturnMock(Mock $mock, Request $request): bool
    {
        return ! $mock->isOptional() || $request->query('_mock') == 1;
    }

    public function resolveParameters(Request $request, $controller, $method): array
    {
        $parameters = [];

        $method = new ReflectionMethod($controller, $method);
        $args = $method->getParameters();
        foreach ($args as $arg) {
            $name = $arg->getName();
            $value = $this->getParameterValue($arg, $request);
            $parameters[$name] = $value;
        }

        return $parameters;
    }

    private function getParameterValue(ReflectionParameter $param, Request $request): mixed
    {
        /**
         * @var \Emsifa\Evo\Contracts\RequestGetter|null $requestGetter
         */
        $requestGetter = ReflectionHelper::getFirstAttributeInstance(
            $param,
            RequestGetter::class,
            ReflectionAttribute::IS_INSTANCEOF,
        );

        if ($requestGetter) {
            if ($requestGetter instanceof RequestValidator) {
                $requestGetter->validateRequest($request, $param);
            }

            return $requestGetter->getRequestValue($request, $param);
        }

        if ($this->isParameterObject($param)) {
            return $this->container->make($param->getType()->getName());
        }

        return $param->isDefaultValueAvailable()
            ? $param->getDefaultValue()
            : null;
    }

    private function isParameterObject(ReflectionParameter $param)
    {
        return $param->getType()
            && $param->getType()->isBuiltin() === false
            && class_exists($param->getType()->getName());
    }

    protected function getRequest(): Request
    {
        return $this->container->make(Request::class);
    }
}
