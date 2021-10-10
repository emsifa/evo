<?php

namespace Emsifa\Evo\Swagger\OpenApi;

use Emsifa\Evo\Contracts\OpenApiOperationModifier;
use Emsifa\Evo\Contracts\OpenApiParameter;
use Emsifa\Evo\Contracts\OpenApiParameterModifier;
use Emsifa\Evo\Contracts\OpenApiRequestBody;
use Emsifa\Evo\Contracts\OpenApiRequestBodyModifier;
use Emsifa\Evo\Contracts\OpenApiResponseModifier;
use Emsifa\Evo\Dto;
use Emsifa\Evo\Helpers\OpenApiHelper;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;
use Emsifa\Evo\Http\Response\ResponseType;
use Emsifa\Evo\Http\Response\UseErrorResponse;
use Emsifa\Evo\Swagger\OpenApi\Concerns\ComponentsResolver;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Contact;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Info;
use Emsifa\Evo\Swagger\OpenApi\Schemas\License;
use Emsifa\Evo\Swagger\OpenApi\Schemas\MediaType;
use Emsifa\Evo\Swagger\OpenApi\Schemas\OpenApi;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Operation;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Path;
use Emsifa\Evo\Swagger\OpenApi\Schemas\RequestBody;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Response;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Server;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route as LaravelRoute;
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
        $openApi->openapi = config('evo.openapi.version', '3.0.3');
        $openApi->info = new Info;
        $openApi->info->title = config('evo.openapi.info.title', "API Documentation");
        $openApi->info->version = config('evo.openapi.info.version', "0.1.0");
        $openApi->info->description = config('evo.openapi.info.description');
        $openApi->info->termsOfService = config('evo.openapi.info.termsOfService');

        $contactConfig = config('evo.openapi.info.contact');
        if ($contactConfig) {
            $contact = new Contact;
            $contact->name = Arr::get($contactConfig, 'name');
            $contact->url = Arr::get($contactConfig, 'url');
            $contact->email = Arr::get($contactConfig, 'email');
            $openApi->info->contact = $contact;
        }

        $licenseConfig = config('evo.openapi.info.license');
        if ($licenseConfig) {
            $license = new License;
            $license->name = Arr::get($licenseConfig, 'name');
            $license->url = Arr::get($licenseConfig, 'url');
            $openApi->info->license = $license;
        }

        $baseUrl = config('app.url', '/');
        $servers = config('evo.openapi.servers', []);

        foreach ($servers as $serverConfig) {
            $server = new Server;
            $server->url = Arr::get($serverConfig, 'url') ?: $baseUrl;
            $server->description = Arr::get($serverConfig, 'description');
            $server->variables = Arr::get($serverConfig, 'variables');
            $openApi->servers[] = $server;
        }

        $routes = $this->getRoutesWithJsonResponse();
        foreach ($routes as $route) {
            [$uri, $methods, $operation] = $this->getPathFromRoute($route);
            $path = Arr::get($openApi->paths, $uri, new Path);
            if (! Arr::has($openApi->paths, $uri)) {
                $openApi->paths[$uri] = $path;
            }

            foreach ($methods as $method) {
                $method = strtolower($method);
                if ($method === 'head') {
                    continue;
                }
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
        return $this->hasJsonResponse($route);
    }

    protected function hasJsonResponse(LaravelRoute $route): bool
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

    protected function getPathFromRoute(LaravelRoute $route): array
    {
        $uri = $route->uri();
        $methods = $route->methods();
        $operation = $this->getOperation($route);

        return ["/".ltrim($uri, "/"), $methods, $operation];
    }

    protected function getOperation(LaravelRoute $route): Operation
    {
        $controller = $route->getAction('controller');
        [$className, $methodName] = explode("@", $controller);
        $class = new ReflectionClass($className);
        $method = new ReflectionMethod($className, $methodName);

        $classOperationModifiers = ReflectionHelper::getAttributesInstances($class, OpenApiOperationModifier::class, ReflectionAttribute::IS_INSTANCEOF);

        $operation = new Operation;
        $operation->operationId = $route->getAction('controller');
        $operation->parameters = $this->getPathParameters($method, $route);
        $operation->requestBody = $this->getRequestBody($method);
        $operation->responses = $this->getResponses($method);

        /**
         * @var OpenApiOperationModifier[] $modifiers
         */
        $modifiers = ReflectionHelper::getAttributesInstances($method, OpenApiOperationModifier::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ([...$classOperationModifiers, ...$modifiers] as $modifier) {
            $modifier->modifyOpenApiOperation($operation);
        }

        return $operation;
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
                $parameter = $openApiParam->getOpenApiParameter($param);

                /**
                 * @var OpenApiParameterModifier[] $modifiers
                 */
                $modifiers = ReflectionHelper::getAttributesInstances($param, OpenApiParameterModifier::class, ReflectionAttribute::IS_INSTANCEOF);
                foreach ($modifiers as $modifier) {
                    $modifier->modifyOpenApiParameter($parameter);
                }

                $openApiParams[] = $parameter;
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
                $modifier->modifyOpenApiRequestBody($body, $param);
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
        $typeNames = array_map(fn ($type) => $type->getName(), $types);
        $exceptionResponses = $this->getExceptionResponses($method);

        $responses = [];
        foreach ([...$typeNames, ...$exceptionResponses] as $className) {
            if (! is_subclass_of($className, JsonResponse::class)) {
                continue;
            }
            $reflectionClass = new ReflectionClass($className);
            $status = $this->getResponseStatusFromClass($reflectionClass);
            $response = $this->getResponseFromClass($reflectionClass, $status);
            $responses[$status] = $response;
        }

        return $responses;
    }

    public function getExceptionResponses(ReflectionMethod $method)
    {
        /**
         * @var UseErrorResponse[] $useErrorResponses
         */
        $useErrorResponses = [
            ...ReflectionHelper::getAttributesInstances($method, UseErrorResponse::class, ReflectionAttribute::IS_INSTANCEOF),
            ...ReflectionHelper::getClassAttributeInstances($method->getDeclaringClass(), UseErrorResponse::class, ReflectionAttribute::IS_INSTANCEOF),
        ];

        $classes = [];

        foreach ($useErrorResponses as $useErrorResponse) {
            $responseClass = $useErrorResponse->getResponseClassName();
            $ifHas = $useErrorResponse->getIfHas();
            $shouldBeAdded = empty($ifHas)
                || ReflectionHelper::hasAttribute($method, $ifHas, ReflectionAttribute::IS_INSTANCEOF)
                || Arr::where($method->getParameters(), fn ($param) => ReflectionHelper::hasAttribute($param, $ifHas, ReflectionAttribute::IS_INSTANCEOF));

            if ($shouldBeAdded) {
                $classes[] = $responseClass;
            }
        }

        return $classes;
    }

    public function getResponseStatusFromClass(ReflectionClass $class): int
    {
        $statuses = ReflectionHelper::getClassAttributes($class->getName(), ResponseStatus::class, ReflectionAttribute::IS_INSTANCEOF);

        if (empty($statuses)) {
            return 200;
        }

        return ($statuses[0]->newInstance())->getStatus();
    }

    public function getResponseFromClass(ReflectionClass $class, int $status): Response
    {
        $types = ReflectionHelper::getClassAttributes($class->getName(), ResponseType::class, ReflectionAttribute::IS_INSTANCEOF);
        $type = $types ? $types[0]->newInstance()->getType() : "application/json";
        $schema = OpenApiHelper::makeSchemaFromClass($class, false);

        $response = new Response;
        $response->description = $this->getDescriptionByStatus($status);
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

    public function getDescriptionByStatus(int $status)
    {
        return match ($status) {
            200 => "OK",
            201 => "Created",
            202 => "Accepted",
            203 => "Non-Authoritative Information",
            204 => "No Content",
            205 => "Reset Content",
            206 => "Partial Content",
            207 => "Multi-Status",
            208 => "Already Reported",
            300 => "Multiple Choice",
            301 => "Moved Permantently",
            302 => "Found",
            303 => "See Other",
            305 => "Use Proxy",
            306 => "Unused",
            307 => "Temporary Redirect",
            308 => "Permanent Redirect",
            400 => "Bad Request",
            401 => "Unauthorized",
            402 => "Payment Required",
            403 => "Forbidden",
            404 => "Not Found",
            405 => "Method not Allowed",
            406 => "Not Acceptable",
            407 => "Proxy Authentication Required",
            408 => "Request Timeout",
            409 => "Conflict",
            410 => "Gone",
            411 => "Length Required",
            412 => "Precondition Failed",
            413 => "Payload Too Large",
            414 => "URI Too Long",
            415 => "Unsupported Media Type",
            416 => "Range Not Satisfiable",
            417 => "Expectation Failed",
            418 => "I'm a teapot",
            421 => "Misdirected Request",
            422 => "Unprocessable Entity",
            423 => "Locked",
            425 => "Too Early",
            426 => "Upgrade Required",
            428 => "Precondition Required",
            429 => "Too Many Requests",
            431 => "Request Header Fields Too Large",
            451 => "Unavailable For Legal Reasons",
            500 => "Internal Server Error",
            501 => "Not Implemented",
            502 => "Bad Gateway",
            503 => "Service Unavailable",
            504 => "Gateway Timeout",
            505 => "HTTP Version Not Supported",
            506 => "Variant Also Negotiates",
            507 => "Insufficient Storage",
            508 => "Loop Detected",
            510 => "Not Extended",
            511 => "Network Authentication Required",
            default => "Undescribed",
        };
    }

    public function getSchemaName(string $className): string
    {
        $className = Str::after("App\\Http\\Responses\\", $className);
        $className = Str::after("App\\Dto\\", $className);

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
            if (is_string($typeName) && is_subclass_of($typeName, Dto::class)) {
                $hasUploadedFile = $this->containsUploadedFile(new ReflectionClass($typeName));
                if ($hasUploadedFile) {
                    return true;
                }
            }
        }

        return false;
    }
}
