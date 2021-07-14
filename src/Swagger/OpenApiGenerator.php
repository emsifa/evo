<?php

namespace Emsifa\Evo\Swagger;

use Emsifa\Evo\Contracts\OpenApiParameter;
use Emsifa\Evo\Contracts\OpenApiPathModifier;
use Emsifa\Evo\DTO;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Http\Body;
use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Route\Route;
use Emsifa\Evo\Swagger\Schemas\PathSchema;
use Emsifa\Evo\Swagger\Schemas\RequestBodySchema;
use Emsifa\Evo\Swagger\Schemas\ResponseSchema;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class OpenApiGenerator
{
    public function __construct(protected Router $router)
    {
    }

    public function generate()
    {
        $routes = $this->getJsonRoutes();
        $paths = [];
        $schemas = [];
        foreach ($routes as $route) {
            [$path, $pathSchemas] = $this->getPathAndSchemas($route);
            if (!array_key_exists($path['uri'], $paths)) {
                $paths[$path['uri']] = [];
            }
            $paths[$path['uri']][strtolower($path['method'])] = $path['schema'];

            foreach ($pathSchemas as $name => $schema) {
                if (!in_array($name, $schemas)) {
                    $schemas[$name] = $schema;
                }
            }
        }
    }

    protected function getJsonRoutes()
    {
        $routes = $this->router->getRoutes()->getRoutes();
        return collect($routes)->filter(fn ($route) => $this->isJsonRoute($route));
    }

    protected function isJsonRoute($route)
    {
        return $route instanceof Route && $this->hasJsonResponse($route);
    }

    protected function hasJsonResponse(Route $route)
    {
        $controller = $route->getAction('controller');
        if (!$controller) {
            return false;
        }

        [$className, $methodName] = explode("@", $controller);
        $method = new ReflectionMethod($className, $methodName);
        $returnType = $method->getReturnType();

        if (!$returnType) {
            return false;
        }

        if ($returnType instanceof ReflectionUnionType) {
            return ReflectionHelper::unionHasType($returnType, JsonResponse::class);
        }

        return is_subclass_of($returnType->getName(), JsonResponse::class);
    }

    protected function getPathAndSchemas(Route $route): array
    {
        $uri = $route->uri();
        $methods = $route->methods();
        $pathSchema = $this->getPathSchema($route);
        $schemas = [];

    }

    protected function getPathSchema(Route $route): PathSchema
    {
        $controller = $route->getAction('controller');
        [$className, $methodName] = explode("@", $controller);
        $method = new ReflectionMethod($className, $methodName);

        $path = new PathSchema;
        $path->operationId = $route->getAction('controller');
        $path->parameters = $this->getPathParameters($method, $route);
        $path->requestBody = $this->getRequestBody($method);
        $path->responses = $this->getResponses($method);
        /**
         * @var OpenApiPathModifier[] $modifiers
         */
        $modifiers = ReflectionHelper::getAttributesInstances($method, OpenApiPathModifier::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($modifiers as $modifier) {
            $modifier->modifyOpenApiPath($path);
        }

        return $path;
    }

    public function getPathParameters(ReflectionMethod $method): array
    {
        $params = $method->getParameters();
        $openApiParams = [];
        foreach ($params as $param) {
            /**
             * @var OpenApiParameter $openApiParam
             */
            $openApiParam = ReflectionHelper::getFirstAttributeInstance($param, OpenApiParameter::class, ReflectionAttribute::IS_INSTANCEOF);
            if ($openApiParam) {
                $openApiParam[] = $openApiParam->getOpenApiParameter($param);
            }
        }
        return $openApiParams;
    }

    public function getRequestBody(ReflectionMethod $method): ?RequestBodySchema
    {
        $params = $method->getParameters();
        $openApiParams = [];
        foreach ($params as $param) {
            /**
             * @var Body $body
             */
            $body = ReflectionHelper::getFirstAttributeInstance($param, Body::class, ReflectionAttribute::IS_INSTANCEOF);
            if ($body) {
                $typeName = optional($param->getType())->getName();
                if ($typeName && class_exists($typeName) && is_subclass_of($typeName, DTO::class)) {
                    $requestBody = new RequestBodySchema;
                    $requestBody->description = $body->getDescription();
                    $requestBody->required = !$param->allowsNull() && !$param->isDefaultValueAvailable();
                    $requestBody->contentSchema = "#/components/schemas/".$this->getSchemaName($typeName);
                    $requestBody->contentType = $this->guessContentType($typeName);
                    return $requestBody;
                }
            }
        }
        return null;
    }

    public function getResponses(ReflectionMethod $method): array
    {
        $returnType = $method->getReturnType();
        if (!$returnType) {
            return [];
        }
        if ($returnType instanceof ReflectionUnionType) {
            return array_map(fn ($type) => $this->getResponseFromType($type), $returnType->getTypes());
        }
        return $this->getResponseFromType($returnType);
    }

    public function getResponseFromType(ReflectionNamedType $type): ResponseSchema
    {
        $response = new ResponseSchema;

        return $response;
    }

    public function getSchemaName(string $className): string
    {
        $className = Str::after("App\\Http\\Responses\\", $className);
        $className = Str::after("App\\DTO\\", $className);
        return str_replace("\\", "", $className);
    }

    public function guessContentType(string $className): string
    {
        return $this->containsUploadedFile(new ReflectionClass($className))
            ? "multipart/form-data"
            : "application/json";
    }

    public function containsUploadedFile(ReflectionClass $reflection): bool
    {
        $props = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            $typeName = optional($prop->getType())->getName();
            if (is_string($typeName) && is_a($typeName, UploadedFile::class)) {
                return true;
            }
            if (is_string($typeName) && is_subclass_of($typeName, DTO::class)) {
                $hasUploadedFile = $this->containsUploadedFile(new ReflectionClass($typeName));
                if ($hasUploadedFile) {
                    return true;
                }
            }
        }
        return false;
    }
}
