---
sidebar_position: 7
---

# Swagger UI



Swagger UI is a web based application to visualize and interacts with API's resources without having any of the implementation logic in place. It reads OpenAPI schema to display and interact with API endpoints.

Evo can reflect your code and generate OpenAPI schema on-the-fly, so you can display Swagger UI with minimal configuration.

In this section we will guide you on how to use Swagger UI with Evo.

## Publishing Assets Files

First, you have to publish assets files that will be used by Swagger UI page.

```php
php artisan vendor:publish --tag=evo-assets
```

## Register Swagger Routes

Then, you have to register two routes. First route is for displaying Swagger UI and second route is for rendering OpenAPI specification as JSON.

To do that, you can simply add this line to your `routes/web.php`:

```php
Evo::swagger('/docs');
```

Now if you run `php artisan route:list`, you should see `GET /docs` and `GET /docs/openapi` routes there.

You can check it by running your app with `php artisan serve`, then in your browser, open URL `http://localhost:8000/docs`.

You should see Swagger UI page with no endpoints yet.

## Add an Example Endpoint

Now we will create an example endpoint to be displayed in our Swagger UI.

First, let's create controller with following command:

```php
php artisan make:controller ExampleController
```

Then, add this to your `routes/api.php`:

```php
Evo::routes(ExampleController::class);
```

Now, let's add a route in our `ExampleController`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Emsifa\Evo\Route\Post;
use Emsifa\Evo\Route\RoutePrefix;

#[RoutePrefix('examples')]
class ExampleController extends Controller
{
    #[Post('post-something')]
    public function postSomething()
    {

    }
}

```

This time Evo will not render any endpoint to Swagger UI because Evo will only register endpoints that returns `Emsifa\Evo\Http\Response\JsonResponse` instance.

Let's create DTO and JsonResponse class for our `postSomething` method by running following commands.

```php
php artisan evo:make-dto PostSomethingDto number:int message:string
php artisan evo:make-response PostSomethingResponse number:int message:string
```

Now we can use it in `postSomething` method like this:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Emsifa\Evo\Route\Post;
use Emsifa\Evo\Route\RoutePrefix;
use App\Dtos\PostSomethingDto;
use App\Http\Responses\PostSomethingResponse;

#[RoutePrefix('examples')]
class ExampleController extends Controller
{
    #[Post('post-something')]
    public function postSomething(
        #[Body] PostSomethingDto $dto
    ): PostSomethingResponse
    {
        return PostSomethingResponse::fromArray($dto);
    }
}
```

Save it. Now if you back to your browser `http://localhost:8000/docs`, you will see there is `POST /api/examples/post-something` operation a.k.a endpoint.

## Configuring OpenAPI

To configure OpenAPI, Evo provides some attributes modifier that modify OpenAPI schema. Also Evo have configuration file to modify some information in OpenAPI.

To configure info with configuration file. First you have to publish configuration file by running following command:

```bash
php artisan vendor:publish --tag=evo-config
```

It will create `config/evo.php` in your project directory. Take a look, and modify it as you want.

### `Example` Attribute

`Emsifa\Evo\Swagger\OpenAPI\Example` attribute is used to define example value to response or DTO's property.

For example, if you expand our `POST /api/examples/post-something` in Swagger UI, you will see our response example value like this:

```json
{
    "number": 0,
    "message": "string"
}
```

If you want to modify it with more realistic example, we can modify `PostSomethingResponse` into something like this:

```php
<?php

namespace App\Http\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Swagger\OpenApi\Example;

class PostSomethingResponse extends JsonResponse
{
    #[Example(12)]
    public int $number;

    #[Example("Lorem dolor sit amet")]
    public string $message;
}
```

Now if you refresh Swagger UI page, you will see that it shows our defined example value.

### `Summary` Attribute

`Emsifa\Evo\Swagger\OpenApi\Summary` attribute is used to define summary to your endpoint operation.

You can attach it to your controller method like this:

```php
#[RoutePrefix('examples')]
class ExampleController extends Controller
{
    #[Post('post-something')]
    #[Summary('Post Something')]
    public function postSomething(
        #[Body] PostSomethingDto $dto
    ): PostSomethingResponse
    {
        return PostSomethingResponse::fromArray($dto);
    }
}
```

Now if you refresh Swagger UI, you will see "Post Something" in `POST /api/examples/post-something` endpoint.

### `Description` Attribute

`Emsifa\Evo\Swagger\OpenApi\Description` attribute is used to define description for operation, schema (properties), response class, or DTO class.

For example we will add description to `PostSomethingResponse` and its properties like following code below:

```php
<?php

namespace App\Http\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Swagger\OpenApi\Example;
use Emsifa\Evo\Swagger\OpenApi\Description;

#[Description("Post something succeed")]
class PostSomethingResponse extends JsonResponse
{
    #[Description("A random number")]
    #[Example(12)]
    public int $number;

    #[Description("A message to stored")]
    #[Example("Lorem dolor sit amet")]
    public string $message;
}
```

Now if you refresh Swagger UI, you will see those description shows there.

## Create Custom OpenAPI Modifiers

To create your own custom OpenAPI modifiers, Evo provides some interfaces that you can implement in your custom attribute class.

Those interfaces are:

* `Emsifa\Evo\Contracts\OpenApiPathModifier`
* `Emsifa\Evo\Contracts\OpenApiSchemaModifier`
* `Emsifa\Evo\Contracts\OpenApiResponseModifier`
* `Emsifa\Evo\Contracts\OpenApiOperationModifier`
* `Emsifa\Evo\Contracts\OpenApiParameterModifier`
* `Emsifa\Evo\Contracts\OpenApiRequestBodyModifier`

For example, you can take a look to `Description` attribute:

```php
<?php

namespace Emsifa\Evo\Swagger\OpenApi;

use Attribute;
use Emsifa\Evo\Contracts\OpenApiOperationModifier;
use Emsifa\Evo\Contracts\OpenApiParameterModifier;
use Emsifa\Evo\Contracts\OpenApiRequestBodyModifier;
use Emsifa\Evo\Contracts\OpenApiResponseModifier;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Operation;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Parameter;
use Emsifa\Evo\Swagger\OpenApi\Schemas\RequestBody;
use Emsifa\Evo\Swagger\OpenApi\Schemas\Response;

#[Attribute]
class Description implements
    OpenApiRequestBodyModifier,
    OpenApiParameterModifier,
    OpenApiOperationModifier,
    OpenApiResponseModifier
{
    public function __construct(protected string $description)
    {
    }

    public function modifyOpenApiRequestBody(RequestBody $body, mixed $reflection = null)
    {
        $body->description = $this->description;
    }

    public function modifyOpenApiParameter(Parameter $parameter)
    {
        $parameter->description = $this->description;
    }

    public function modifyOpenApiOperation(Operation $operation)
    {
        $operation->description = $this->description;
    }

    public function modifyOpenApiResponse(Response $response)
    {
        $response->description = $this->description;
    }
}
```
