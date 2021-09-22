---
sidebar_position: 6
---

# Response

Evo provide custom response class `Emsifa\Evo\Http\Response\JsonResponse` and `Emsifa\Evo\Http\Response\ViewResponse` that is inherit from `Emsifa\Evo\Dto` class.
They are all implements `Illuminate\Contracts\Support\Responsable`, so that they can be transformed to HTTP response.

You can extend them to your response classes then use your response classes as return type of your controller action to get these benefits below:

* Easier to identify how the response data should be.
* It prevents you to send wrong data types. Eg: integer as string, null to non-nullable property, etc.
* It makes Evo knows how to generate your endpoint into OpenAPI.
* It makes Evo knows how to mock the response.

## Generate Response Class

Evo provide `evo:make-response` command to generate response class. Here is its command signature:

```bash
php artisan evo:make-response {classname} {...properties} {--view} {--json-template=} 
```

* `classname` (required): class name to be generated.
* `properties` (optional): class properties.
* `--view` (optional): by default `evo:make-response` command will use `JsonResponse` as parent class, if you want to use `ViewResponse`, you can add this option.
* `--json-template=`: add this if you want to apply `UseJsonTemplate` attribute in generated response class.

For example, we will generate `StoreTodoResponse` with command below:

```bash
php artisan evo:make-response StoreTodoResponse id:int title:string completed:bool
```

It will generate `app/Http/Responses/StoreTodoResponse` class with following code below:

```php
<?php

namespace App\Http\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;

class StoreTodoResponse extends JsonResponse
{
    public int $id;
    public string $title;
    public bool $completed;
}
```

## Using Response Class

To use `StoreTodoResponse` you need to put it as return type, then in your controller you can use `fromArray` method like an example below:

```php
#[Post]
public function store(#[Body] StoreTodoDto $dto): StoreTodoResponse
{
    $todo = Todo::create($dto);

    return StoreTodoResponse::fromArray($todo);
}
```

`StoreTodoResponse::fromArray` method will create `StoreTodoResponse` instance by mapping `StoreTodoResponse` public properties with `$todo` array values. It also apply type casting for each properties.

## Using View Response

In this example we will create view response in Evo's way.

First let's generate our response view with following command:

```bash
php artisan evo:make-response EditTodoResponse todo:TodoDto --view
```

Since you put `TodoDto` type there, it will generate 2 files below:

1. `app/Http/Responses/EditTodoResponse`:

    ```php
    <?php

    namespace App\Http\Responses;

    use Emsifa\Evo\Http\Response\UseView;
    use Emsifa\Evo\Http\Response\ViewResponse;

    #[UseView('edit-todo')]
    class EditTodoResponse extends ViewResponse
    {
        public TodoDto $todo;
    }
    ```
2. `app/Http/Responses/TodoDto`:

    ```php
    <?php

    namespace App\Http\Responses;

    use Emsifa\Evo\Http\Response\ResponseDto;

    class TodoDto extends ResponseDto
    {
    }
    ```

`UseView` attribute above is used to identify what view file should be rendered, and the data passed to the view is its properties, in this case `$todo` that is `TodoDto` instance.

Now, let's edit `TodoDTO` with following properties:

```php
<?php

namespace App\Http\Responses;

use DateTime;
use Emsifa\Evo\Http\Response\ResponseDTO;

class TodoDTO extends ResponseDTO
{
    public int $id;
    public string $title;
    public bool $is_completed;
    public DateTime $created_at;
    public ?DateTime $updated_at;
}
```

And here is the example on how to use `EditTodoResponse` view:

```php
#[Get('{id}')]
public function edit(#[Param] int $id): EditTodoResponse
{
    $todo = Todo::findOrFail($id);

    return EditTodoResponse::fromArray(['todo' => $todo]);
}
```

Yes, Evo will automatically transform your `Todo` model into `TodoDto` instance.

## Define Response Status

To define response status in your response class, you can attach `Emsifa\Evo\Http\Response\ResponseStatus` attribute in your response class like following code below:

```php
<?php

namespace App\Http\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\ResponseStatus;

#[ResponseStatus(201)]
class StoreTodoResponse extends JsonResponse
{
    public int $id;
    public string $title;
    public bool $completed;
}
```

Now, when you return `StoreTodoResponse`, Evo will set response status to `201`.

## Using Json Template

Evo provide `Emsifa\Evo\Contracts\JsonTemplate` class to wrap your `JsonResponse` instance.

When building RESTful API you may have a convention/standard on how your data formatted.

For example we want our `StoreTodoResponse` instead of just rendered like this:

```json
{
    "id": 1,
    "title": "Write documentation",
    "completed": false
}
```

We want it to be wrapped like this:

```json
{
    "status": "ok",
    "data": {
        "id": 1,
        "title": "Write documentation",
        "completed": false
    }
}
```

To do this, we have to create a class that implements `Emsifa\Evo\Contracts\JsonTemplate`.
This class will be used as a wrapper to all of your JSON response classes.

Let's create file `app/Http/Responses/SuccessJsonTemplate.php` with following code:

```php
<?php

namespace App\Http\Responses;

use Emsifa\Evo\Contracts\JsonData;
use Emsifa\Evo\Contracts\JsonTemplate;
use Emsifa\Evo\Http\Response\JsonResponse;

class SuccessJsonTemplate implements JsonTemplate
{
    public string $status;
    public JsonData $data;

    /**
     * @param BaseResponse $response
     */
    public function forJsonResponse(JsonResponse $response): static
    {
        $this->status = "ok";
        $this->data = $response->getData();

        return $this;
    }
}
```

Now you can use this template using `Emsifa\Evo\Http\Response\UseJsonTemplate` like following code below:

```php {6,8}
<?php

namespace App\Http\Responses;

use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Evo\Http\Response\UseJsonTemplate;

#[UseJsonTemplate(SuccessJsonTemplate::class)]
class StoreTodoResponse extends JsonResponse
{
    public int $id;
    public string $title;
    public bool $completed;
}
```

Now whenever you return `StoreTodoResponse` in your controller, it will be wrapped with `SuccessJsonTemplate` data.
