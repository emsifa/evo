<?php

namespace Emsifa\Evo;

use Emsifa\Evo\Contracts\ExceptionResponse;
use Emsifa\Evo\Contracts\RequestGetter;
use Emsifa\Evo\Contracts\RequestValidator;
use Emsifa\Evo\Error\DontReport;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Http\Response\Mock;
use Emsifa\Evo\Http\Response\UseErrorResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerDispatcher as BaseControllerDispatcher;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use ReflectionAttribute;
use ReflectionClass;
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
        if ($mock && $mock->shouldBeUsed($request)) {
            return $mock->getMockedResponse($this->container, $methodReflection, $request);
        }

        try {
            return call_user_func_array([$controller, $method], $parameters);
        } catch (Exception $exception) {
            $mapErrorResponses = $this->getErrorResponsesMap($methodReflection);
            $exceptionResponse = $this->makeExceptionResponse($exception, $mapErrorResponses);

            if (! $exceptionResponse) {
                throw $exception;
            }

            $exceptionReflection = new ReflectionClass($exception);
            $shouldNotReported = ReflectionHelper::hasAttribute($exceptionReflection, DontReport::class, ReflectionAttribute::IS_INSTANCEOF);
            if ($shouldNotReported === false) {
                report($exception);
            }

            return $exceptionResponse;
        }
    }

    public function getErrorResponsesMap(ReflectionMethod $method): array
    {
        /**
         * @var UseErrorResponse[] $useErrorResponses
         */
        $useErrorResponses = [
            ...ReflectionHelper::getAttributesInstances($method, UseErrorResponse::class, ReflectionAttribute::IS_INSTANCEOF),
            ...ReflectionHelper::getClassAttributeInstances($method->getDeclaringClass(), UseErrorResponse::class, ReflectionAttribute::IS_INSTANCEOF),
        ];

        $map = [];
        foreach ($useErrorResponses as $useErrorResponse) {
            $exceptions = $useErrorResponse->getExceptionClassNames();
            $responseClass = $useErrorResponse->getResponseClassName();
            if (empty($exceptions) && ! Arr::has($map, '_')) {
                $map['_'] = $responseClass;
            } else {
                foreach ($exceptions as $exception) {
                    if (! Arr::has($map, $exception)) {
                        $map[$exception] = $responseClass;
                    }
                }
            }
        }

        return $map;
    }

    public function makeExceptionResponse(Exception $exception, array $mapExceptionResponses)
    {
        if (empty($mapExceptionResponses)) {
            return null;
        }

        $responseClassName = $this->findBestMatchErrorResponse($exception, $mapExceptionResponses);

        if (is_null($responseClassName)) {
            return null;
        }

        $response = $this->container->make($responseClassName);

        if ($response instanceof ExceptionResponse) {
            $response->forException($exception);
        } else {
            ObjectFiller::fillObject($response, [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]);
        }

        return $response;
    }

    public function findBestMatchErrorResponse(Exception $exception, array $mapExceptionResponses): ?string
    {
        $targetExceptionName = get_class($exception);

        if (Arr::has($mapExceptionResponses, $targetExceptionName)) {
            return Arr::get($mapExceptionResponses, $targetExceptionName);
        }

        foreach ($mapExceptionResponses as $exceptionClassName => $responseClassName) {
            if (is_subclass_of($targetExceptionName, $exceptionClassName)) {
                return $responseClassName;
            }
        }

        if (Arr::has($mapExceptionResponses, '_')) {
            return Arr::get($mapExceptionResponses, '_');
        }

        return null;
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
