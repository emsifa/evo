---
sidebar_position: 8
---

# Mocking API

Mocking API is a feature that allows your controller to send response with fake data.

When developing REST API with a team consisting of Back-end and Front-end Developers. Creating an API implementation could takes time. Sometimes it makes Front-end developer have to wait Back-end Developer to finish the implementation. This will make development time less efficient.

To make development time more efficient. We can use Mocking API so Front-end Developer can consume our API without waiting for real implementation to be done.

To do that, in Evo you can simply attach `Emsifa\Evo\Http\Response\Mock` attribute to your controller method. So when user call that endpoint, Evo will prevent its controller to be executed, instead Evo will read its return type, create return type instance and fill its properties with fake data, finally Evo will respond that fake instance.

## Using `Mock` Attribute

For example, we will use `ExampleController` in Swagger UI section before.

To respond mock on `postSomething` method, we need to attach `Emsifa\Evo\Http\Response\Mock` like an example below:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Emsifa\Evo\Route\Post;
use Emsifa\Evo\Route\RoutePrefix;
use Emsifa\Evo\Http\Response\Mock;
use App\Dtos\PostSomethingDto;
use App\Http\Responses\PostSomethingResponse;

#[RoutePrefix('examples')]
class ExampleController extends Controller
{
    #[Post('post-something')]
    #[Mock]
    public function postSomething(
        #[Body] PostSomethingDto $dto
    ): PostSomethingResponse
    {
        return PostSomethingResponse::fromArray($dto);
    }
}
```

That's it!

Now to try this, open your Swagger UI page. Expand `POST /api/examples/post-something` endpoint, click `Try it out`, then click execute.

You should see it responded with random data.

## Using Specific Faker

Behind the scene Evo uses Faker to generate mock data. By default Evo choosing faker to be used is by looking the data type and the name of each property.

For example, if you have `$name` property, Evo will use `$faker->name()`. For `$title` property, Evo will use `$faker->title()`. For `float $latitude`, Evo will use `$faker->latitude()`. If the property name doesn't have related faker formatter available, Evo will choose faker formatter by it's data type.

If you want to use another faker instead of default choosed faker. You can use `Emsifa\Evo\Dtos\UseFaker` to define what formatter you want to use.

For example, we will use `paragraph(5)` formatter to `$message` property.

Here is the code:

```php
<?php

namespace App\Http\Responses;

use Emsifa\Evo\Dtos\UseFaker;
use Emsifa\Evo\Http\Response\JsonResponse;

#[Description("Post something succeed")]
class PostSomethingResponse extends JsonResponse
{
    public int $number;

    #[UseFaker("paragraph", 5)]
    public string $message;
}
```

Now when you execute your endpoint again, it will respond `message` with paragraph contains 5 sentences.