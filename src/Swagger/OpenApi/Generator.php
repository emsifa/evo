<?php

namespace Emsifa\Evo\Swagger\OpenApi;

use Emsifa\Evo\Contracts\OpenApiParameter;
use Emsifa\Evo\Contracts\OpenApiRequestBody;
use Emsifa\Evo\Contracts\OpenApiRequestBodyModifier;
use Emsifa\Evo\Contracts\OpenApiResponseModifier;
use Emsifa\Evo\DTO;
use Emsifa\Evo\Helpers\OpenApiHelper;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;
use Emsifa\Evo\Http\Response\ResponseType;
use Emsifa\Evo\Route\Route;
use Emsifa\Evo\Swagger\OpenApi\Concerns\ComponentsResolver;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Info;
use Emsifa\Evo\Swagger\OpenApi\Schemas\MediaType;
use Emsifa\Evo\Swagger\OpenApi\Schemas\OpenApi;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Operation;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Path;
use Emsifa\Evo\Swagger\OpenApi\Schemas\RequestBody;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionUnionType;

class Generator
{
    use ComponentsResolver;

    protected Router $router;

    public function __construct(protected Application $app)
    {
        $this->router = $app->make(Router::class);
    }

    public function generateToJson(): string
    {
        return json_encode($this->getResultArray());
    }

    public function getResultArray(): array
    {
        return $this->makeOpenApi()->toArray();
    }

    public function makeOpenApi(): OpenApi
    {
        /**
         * @var OpenApi $openApi
         */
        $openApi = $this->app->has(OpenApi::class) ? $this->app->make(OpenApi::class) : new OpenApi;
        $openApi->openapi = "3.0.1";
        $openApi->info = new Info;
        $openApi->info->title = "API Documentation";
        $openApi->info->version = "0.1.0";

        $routes = $this->getRoutesWithJsonResponse();
        foreach ($routes as $route) {
            [$uri, $methods, $operation] = $this->getPathFromRoute($route);
            $path = Arr::get($openApi->paths, $uri, new Path);
            if (! Arr::has($openApi->paths, $uri)) {
                $openApi->paths[$uri] = $path;
            }

            foreach ($methods as $method) {
                $method = strtolower($method);
                $path->{$method} = $operation;
            }
        }

        $this->resolveComponents($openApi);

        return $openApi;
    }

    protected function getRoutesWithJsonResponse(): Collection
    {
        $routes = $this->router->getRoutes()->getRoutes();

        return collect($routes)->filter(fn ($route) => $this->isJsonRoute($route));
    }

    protected function isJsonRoute($route): bool
    {
        return $route instanceof Route && $this->hasJsonResponse($route);
    }

    protected function hasJsonResponse(Route $route): bool
    {
        $controller = $route->getAction('controller');
        if (! $controller) {
            return false;
        }

        [$className, $methodName] = explode("@", $controller);
        $method = new ReflectionMethod($className, $methodName);
        $returnType = $method->getReturnType();

        if (! $returnType) {
            return false;
        }

        if ($returnType instanceof ReflectionUnionType) {
            return ReflectionHelper::unionHasType($returnType, JsonResponse::class);
        }

        return is_subclass_of($returnType->getName(), JsonResponse::class);
    }

    protected function getPathFromRoute(Route $route): array
    {
        $uri = $route->uri();
        $methods = $route->methods();
        $operation = $this->getOperation($route);

        return [$uri, $methods, $operation];
    }

    protected function getOperation(Route $route): Operation
    {
        $controller = $route->getAction('controller');
        [$className, $methodName] = explode("@", $controller);
        $method = new ReflectionMethod($className, $methodName);

        $path = new Operation;
        $path->operationId = $route->getAction('controller');
        $path->parameters = $this->getPathParameters($method, $route);
        $path->requestBody = $this->getRequestBody($method);
        $path->responses = $this->getResponses($method);

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
                $openApiParams[] = $openApiParam->getOpenApiParameter($param);
            }
        }

        return $openApiParams;
    }

    public function getRequestBody(ReflectionMethod $method): ?RequestBody
    {
        $params = $method->getParameters();
        $body = null;
        foreach ($params as $param) {
            /**
             * @var OpenApiRequestBody $body
             */
            $bodyAttr = ReflectionHelper::getFirstAttributeInstance($param, OpenApiRequestBody::class, ReflectionAttribute::IS_INSTANCEOF);
            if ($bodyAttr) {
                $body = $bodyAttr->getOpenApiRequestBody($param);
            }
            /**
             * @var OpenApiRequestBodyModifier[] $modifiers
             */
            $modifiers = ReflectionHelper::getAttributesInstances($param, OpenApiRequestBodyModifier::class, ReflectionAttribute::IS_INSTANCEOF);
            if ($modifiers && ! $body) {
                $body = new RequestBody;
            }
            foreach ($modifiers as $modifier) {
                $modifier->modifyOpenApiRequestBody($body);
            }
        }

        return $body;
    }

    public function getResponses(ReflectionMethod $method): array
    {
        $returnType = $method->getReturnType();
        if (! $returnType) {
            return [];
        }
        $types = $returnType instanceof ReflectionUnionType ? $returnType->getTypes() : [$returnType];
        $responses = [];
        foreach ($types as $type) {
            $className = $type->getName();
            if (! is_subclass_of($className, JsonResponse::class)) {
                continue;
            }
            $reflectionClass = new ReflectionClass($className);
            $status = $this->getResponseStatusFromClass($reflectionClass);
            $response = $this->getResponseFromClass($reflectionClass);
            $responses[$status] = $response;
        }

        return $responses;
    }

    public function getResponseStatusFromClass(ReflectionClass $class): int
    {
        $statuses = ReflectionHelper::getClassAttributes($class->getName(), ResponseStatus::class, ReflectionAttribute::IS_INSTANCEOF);

        if (empty($statuses)) {
            return 200;
        }

        return ($statuses[0]->newInstance())->getStatus();
    }

    public function getResponseFromClass(ReflectionClass $class): Response
    {
        $types = ReflectionHelper::getClassAttributes($class->getName(), ResponseType::class, ReflectionAttribute::IS_INSTANCEOF);
        $type = $types ? $types[0]->newInstance()->getType() : "application/json";
        $schema = OpenApiHelper::makeSchemaFromClass($class, false);

        $response = new Response;
        $response->content = [$type => new MediaType(schema: $schema)];

        /**
         * @var OpenApiResponseModifier[] $modifiers
         */
        $modifiers = ReflectionHelper::getAttributesInstances($class, OpenApiResponseModifier::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($modifiers as $modifier) {
            $modifier->modifyOpenApiResponse($response);
        }

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
            if (is_string($typeName) && is_a($typeName, UploadedFile::class, true)) {
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