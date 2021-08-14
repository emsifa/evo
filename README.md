<div align="center">
    <img alt="Logo" src="https://raw.githubusercontent.com/emsifa/evo/main/logo.svg" height="100px"/>
</div>

<div align="center">
    
[![Latest Version on Packagist](https://img.shields.io/packagist/v/emsifa/evo.svg?style=flat-square)](https://packagist.org/packages/emsifa/evo)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/emsifa/evo/run-tests?label=tests&style=flat-square)](https://github.com/emsifa/evo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Coverage Status](https://img.shields.io/coveralls/github/emsifa/evo?label=coveralls&style=flat-square)](https://coveralls.io/github/emsifa/evo?branch=main)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/emsifa/evo/Check%20&%20fix%20styling?label=code%20style&style=flat-square)](https://github.com/emsifa/evo/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/emsifa/evo.svg?style=flat-square)](https://packagist.org/packages/emsifa/evo)
    
</div>

---

Evo is a Laravel package that leverages PHP 8 features. It change the way you write Laravel app into something like this:

```php
#[RoutePrefix('users')]
class UserController extends Controller
{
    #[Get]
    public function index(
        #[Query] int $limit,
        #[Query] int $offset,
        #[Query('q')] string $keyword,
    ): UserPaginationResponse
    {
        // your logic goes here
    }

    #[Post]
    public function store(
        #[Body] CreateUserDto $data
    ): StoreUserResponse
    {
        // your logic goes here
    }
    
    #[Put('{id}')]
    public function update(
        #[Param] int $id,
        #[Body] UpdateUserDto $data,
    ): UpdateUserResponse
    {
        // your logic goes here
    }
}
```

## Motivation

Defining input and output types in a head of a function will trigger your brain to specifies input and output types before writing the logic. So when it comes to write the logic, you know exactly what you have, where it comes, and what to return.

Also, by defining input and output type in this way, not only you and your teammate would easily read the specifications. Machines too. That is why Evo can provide some amazing features such as auto validation, auto casting, live swagger documentation, mocking API, etc.

## Features

* [x] [Register routes using attributes](#register-route).
* [x] [Apply middleware using attribute](#applying-middleware). 
* [x] [Route prefixing using attribute](#route-prefixing). 
* [x] [Inject request data (Header, Param, Cookie, Body, Query) into parameters using attribute](#accessing-request-value).
* [x] Automatic type casting.
* [x] Automatic type validation.
* [x] [Define validation rules directly in DTO properties using attribute](#validating-body).
* [x] [Custom value caster](#creating-custom-type-caster).
* [x] [Generate DTO file](#generating-dto-file).
* [x] [Generate Response file](#generate-response-class).
* [x] [Generate Swagger UI and OpenAPI file](#swagger-ui).
* [x] [Mocking API](#mocking-api).


## Installation

> Evo currently is still in the development, it could have some breaking changes before the final release.

You can install the package via composer:

```bash
composer require emsifa/evo:dev-main
```

## Usage

### Routing

#### Register Route

To be able to use Laravel Controller in Evo's way, you have to register route with `EvoFacade::routes` method like below:

```php
// routes/web.php or routes/api.php

use Emsifa\Evo\EvoFacade as Evo;

Evo::routes(App\Http\Controllers\UserController::class);
```

Then in your `UserController`, you can attach route attribute such as `Get`, `Post`, `Put`, `Patch`, `Delete` like an example below:

```php
<?php

namespace App\Http\Controllers;

use Emsifa\Evo\Route\Get;
use Emsifa\Evo\Route\Post;
use Emsifa\Evo\Route\Put;
use Emsifa\Evo\Route\Delete;

class UserController extends Controller
{
    #[Get('users')]
    public function index()
    {
        // ...
    }
    
    #[Post('users')]
    public function store()
    {
        // ...
    }
    
    #[Get('users/{id}')]
    public function show($id)
    {
        // ...
    }
    
    #[Put('users/{id}')]
    public function update($id)
    {
        // ...
    }
    
    #[Delete('users/{id}')]
    public function destroy($id)
    {
        // ...
    }
}
```

#### Route Prefixing

If you want to apply route prefix into your controller, you can attach `RoutePrefix` attribute to  your controller class.

```php
<?php

namespace App\Http\Controllers;

use Emsifa\Evo\Route\RoutePrefix;
use Emsifa\Evo\Route\Get;
use Emsifa\Evo\Route\Post;
use Emsifa\Evo\Route\Put;
use Emsifa\Evo\Route\Delete;

#[RoutePrefix('users')]
class UserController extends Controller
{
    #[Get]
    public function index()
    {
        // ...
    }
    
    #[Post]
    public function store()
    {
        // ...
    }
    
    #[Get('{id}')]
    public function show($id)
    {
        // ...
    }
    
    #[Put('{id}')]
    public function update($id)
    {
        // ...
    }
    
    #[Delete('{id}')]
    public function destroy($id)
    {
        // ...
    }
}
```

#### Applying Middleware

Every route attribute has `$middleware` parameter that you can set to apply middleware. Here is some examples:

```php
<?php

namespace App\Http\Controllers;

use Emsifa\Evo\Route\RoutePrefix;
use Emsifa\Evo\Route\Get;

#[RoutePrefix('users')]
class UserController extends Controller
{
    #[Get("/", middleware: "auth")]
    public function index()
    {
        // ...
    }
    
    #[Post("/", middleware: ["auth", "can:store-post"])]
    public function store()
    {
        // ...
    }
}
```

If you want to apply same middleware to every routes in a controller, you can attach `RouteMiddleware` to your controller class like an example below:

```php
<?php

namespace App\Http\Controllers;

use Emsifa\Evo\Route\RoutePrefix;
use Emsifa\Evo\Route\RouteMiddleware;
use Emsifa\Evo\Route\Get;

#[RoutePrefix('users')]
#[RouteMiddleware('auth')]
class UserController extends Controller
{
    #[Get]
    public function index()
    {
        // ...
    }
    
    #[Post(middleware: "can:store-post")]
    public function store()
    {
        // ...
    }
}
```

### Accessing Request Value

In Evo, you can access request value by attaching request attributes such as `Query`, `Cookie`, `Header`, `Param`, `Body`, etc to your method parameters. Then Evo will automatically inject corresponding value to attached parameter. Evo will also automatically validate and cast the value according to parameter type and definition.

Before using those attributes, make sure you have import their full class names.

```php
use Emsifa\Evo\Http\Param;
use Emsifa\Evo\Http\Query;
use Emsifa\Evo\Http\Header;
use Emsifa\Evo\Http\Cookie;
use Emsifa\Evo\Http\Body;
```

#### `Query` Attribute

`Query` attribute is used to get a query value from HTTP request.

```php
#[Get]
public function index(#[Query] int $page)
{
    // ...
} 
```

In example above, Evo will:

1. Getting `request()->query('page')` value.
2. Apply validation to the value to make sure it's numeric.
3. Cast value to `int`.
4. Inject casted value to `$page` parameter.

If you want to use different query and parameter name, you can set `$key` parameter to `Query` attribute like an example below:

```php
#[Get]
public function index(#[Query('p')] int $page)
{
    // ...
} 
```

In example above Evo will get `p` query value and inject it to `$page` parameter.

If you want to make it optional, you can just give default value to the parameter. For example:

```php
#[Get]
public function index(#[Query('q')] ?string $keyword = null)
{
    // ...
} 
```

#### `Param` Attribute

`Param` attribute is used to get URI parameter value.

```php
#[Get('users/{id}')]
public function index(#[Param] int $id)
{
    // ...
} 
```

Like `Query` attribute before, Evo will do validation, type casting, and inject the value to the `$id` parameter. 

#### `Header` Attribute

`Header` attribute is used to get a header value from HTTP request.

```php
#[Get('users/{id}')]
public function index(#[Header('user-agent')] string $userAgent)
{
    // ...
} 
```

In example above, Evo will get `user-agent` header value and inject it to the `$userAgent` parameter.


#### `Cookie` Attribute

`Cookie` attribute is used to get a cookie value from HTTP request.

```php
#[Get('users/{id}')]
public function index(#[Cookie] string $token)
{
    // ...
} 
```

In example above, Evo will get `token` cookie value and inject it to the `$token` parameter.


#### `Body` Attribute

`Body` attribute is used to get value from HTTP request body.
To use `Body` attribute you have to use `DTO` class as the type of your parameter.
You can create `DTO` class by using `evo:make-dto` command.

In this example, we will inject request body value as `RegisterDto` instance into `register` method.

First, we have to generate `RegisterDto` class with command:

```bash
php artisan evo:make-dto RegisterDto name:string email:string password:string password_confirmation:string
```

Then you will get `app/Dtos/RegisterDto.php` file with code like this:

```php
<?php

namespace App\Dto;

use Emsifa\Evo\Dto;

class RegisterDto extends Dto
{
    public string $name;
    public string $email;
    public string $password;
    public string $password_confirmation;
}
```

Now you may want to add some extra validations to each properties. You can do that by attaching rules attribute like an example below:


```php
<?php

namespace App\Dto;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Rules;

class RegisterDto extends Dto
{
    #[Rules\Required]
    public string $name;

    #[Rules\Required]
    #[Rules\Email(message: "Incorrect email format")]
    #[Rules\Unique(table: 'users', column: 'email', message: "Email already used by someone")]
    public string $email;

    #[Rules\Required]
    #[Rules\Min(6, message: "Password must have at least 6 characters")]
    public string $password;

    #[Rules\Required]
    #[Rules\SameWith('password', message: "Password confirmation doesn't match with password")]
    public string $password_confirmation;
}
```

Behind the scene, code above will do validation like Laravel code below:

```php
$request->validate([
    'name' => 'required',
    'email' => 'required|email|unique:users,email',
    'password' => 'required|min:6',
    'password_confirmation' => 'required|same:password',
], [
    'email.email' => "Incorrect email format",
    'email.unique' => "Email already used by someone",
    'password.min' => "Password must have at least 6 characters",
    'password_confirmation.same' => "Password confirmation doesn't match with password",
]);
```

After defining validation rules to properties, you can inject `RegisterDto` instance to your method like any other attributes before, but with `Body` attribute.

```php
#[Post('register')]
public function register(#[Body] RegisterDto $dto)
{
    // ...
}
```

And there you go. Same like any other attributes. Evo will validate the values, resolving `RegisterDto` instance, and inject it to `$dto` parameter.

#### Uploaded File

You can get uploaded files in two ways below:

1. Using DTO class and `Body` attribute.
2. Using `File` attribute.

##### 1. Getting uploaded file using DTO and `Body` attribute

To get uploaded file using DTO, you can just add a property with type `Illuminate\Http\UploadedFile`.

```php
<?php

namespace App\Dto;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Rules;
use Illuminate\Http\UploadedFile;

class UpdateProfileDto extends Dto
{
    #[Rules\Required]
    public string $name;

    #[Rules\Mimes(['jpeg', 'png'])]
    #[Rules\Image]
    public ?UploadedFile $avatar = null;
}
```

In example above Evo will inject `$avatar` property with `request()->file('avatar')` value.

##### 2. Getting uploaded file using `File` attribute

If you want directly inject uploaded file instance to your controller method, you can use `File` attribute like an example below:

```php
#[Post('register')]
public function register(
    #[Body] RegisterDto $dto,
    #[File(rules: 'required|mimes:jpeg,png|image')] UploadedFile $avatar,
)
{
    // ...
}
```

#### `LoggedUser` Attribute

`LoggedUser` attribute is used to get current logged user instance from `request()->user()`.
You can use it like any other request attributes before, just make sure if its optional, you have to make it nullable and set default value to null.

```php
public function show(
    #[Param] int $id,
    #[LoggedUser] ?User $user = null,
)
{
    // ...
}
```

#### Creating Custom Request Attribute

You can create your own request getter attribute by creating a class implementing `Emsifa\Evo\Contracts\RequestGetter` interface.

For example we will create `JwtToken` attribute to retrieve JWT token from header or cookie.

First, create a file `app/Http/Attributes/JwtToken.php`.

Then you can use code below:

```php
<?php

namespace App\Http\Attributes;

use Attribute;
use Emsifa\Evo\Contracts\RequestGetter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ReflectionParameter;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PARAMETER)]
class JwtToken implements RequestGetter
{
    public function getRequestValue(Request $request, ReflectionParameter | ReflectionProperty $reflection): mixed
    {
        $token_from_header = $request->header("authorization");
        $token_from_cookie = $request->cookie("token");

        return $token_from_header
            ? Str::after($token_from_header, "Bearer ")
            : $token_from_cookie;
    }
}
```

Now you can use it like any other request attributes like following code:

```php
public function doSomething(#[JwtToken] ?string $token = null)
{
    // ...
}
```

In most case you may want to validate the value to make sure it is safe for Evo to type cast it and inject it to your parameter.
To do that, you can implement `Emsifa\Evo\Contracts\RequestValidator` like an example below:


```php
<?php

namespace App\Http\Attributes;

use Attribute;
use Emsifa\Evo\Contracts\RequestGetter;
use Emsifa\Evo\Contracts\RequestValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use ReflectionParameter;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PARAMETER)]
class JwtToken implements RequestGetter, RequestValidator
{
    public function getRequestValue(Request $request, ReflectionParameter | ReflectionProperty $reflection): mixed
    {
        $token_from_header = $request->header("authorization");
        $token_from_cookie = $request->cookie("token");

        return $token_from_header
            ? Str::after($token_from_header, "Bearer ")
            : $token_from_cookie;
    }

    public function validateRequest(Request $request, ReflectionProperty | ReflectionParameter $reflection)
    {
        $data = [
            "token" => $this->getRequestValue($request, $reflection)
        ];

        $rules = [
            "token" => "required|string",
        ];

        Validator::make($data, $rules)->validate();
    }
}
```

Now Evo will run `validateRequest` before type casting it's value and inject it to your parameter.

### Using DTO

DTO (Data Transfer Object) is basically an object that contains some declared properties in it. DTO used to carry data between processes. In typical PHP application, we often store data as associative array or as `stdClass`. The downside of storing data as associative array or `stdClass` is we don't really know what is inside and what type it is. If we are not carefully to check it, it could cause some security problem to our application.

By replacing them with DTO, we, Text Editor, and IDE know exactly what it is, what properties in it, the type of each properties, etc.

But creating DTO instance with PHP native way sometimes can be quite annoying. You have to create an instance, set the value for each properties one by one, also you have to cast its type properly.

That is why Evo provide `Emsifa\Evo\Dto` class that comes with some useful utilities.

In the [`Body` Attribute](#body-attribute) section, we are creating `RegisterDto` class that will be injected using `Body` attribute. Yes, the validation process does comes from `Body` attribute, but the ability to create `RegisterDto` instance with the correct types for its properties is comes from its parent class: `Emsifa\Evo\Dto`.

`Emsifa\Evo\Dto` doing type casting by looking for type casters attached to it. If you look at the source of `Emsifa\Evo\Dto`, you will see something like this:

```php
#[UseCaster('int', IntCaster::class)]
#[UseCaster('float', FloatCaster::class)]
#[UseCaster('string', StringCaster::class)]
#[UseCaster('bool', BoolCaster::class)]
#[UseCaster('array', ArrayCaster::class)]
#[UseCaster(DateTime::class, DateTimeCaster::class)]
#[UseCaster(Collection::class, CollectionCaster::class)]
abstract class Dto implements Arrayable
{
    ...
}
```

In code above, `UseCaster` is used to tell the DTO what caster class to be used to cast a type.

In this section we will explore how to generate DTO file, how default casters behave, and how to create and override default caster.

#### Generating DTO File

We can generate DTO file using `evo:make-dto` command. Below is the signature of the command:

```bash
php artisan evo:make-dto {classname} {...properties}
```

Argument `classname` is required and `properties` are optional.

For example, to create `LoginDto` that have `string $email` and `string $password` properties. We should run:

```bash
php artisan evo:make-dto LoginDto email:string password:string
```

It will generate `app/Dtos/LoginDto.php` with code:

```php
<?php

namespace App\Dto;

use Emsifa\Evo\Dto;

class LoginDto extends Dto
{
    public string $email;
    public string $password;
}

```

You can also generate nested object by giving the name of the object for the type. For example:

```bash
php artisan evo:make-dto SaveProfileDto name:string contact:ContactDto address:AddressDto
```

It will generate 3 files below: 

1. `app/Dtos/SaveProfileDto.php`:
    ```php
    <?php

    namespace App\Dto;

    use Emsifa\Evo\Dto;

    class SaveProfileDto extends Dto
    {
        public string $name;
        public ContactDto $contact;
        public AddressDto $contact;
    }
    ```
2. `app/Dtos/ContactDto.php` (if not exists):
    ```php
    <?php

    namespace App\Dto;

    use Emsifa\Evo\Dto;

    class ContactDto extends Dto
    {
    }
    ```

3. `app/Dtos/AddressDto.php` (if not exists):
    ```php
    <?php

    namespace App\Dto;

    use Emsifa\Evo\Dto;

    class AddressDto extends Dto
    {
    }
    ```

You can also generate typed array by adding `[]` sign after type name. For example:

```bash
php artisan evo:make-dto CheckoutOrderDto items:CheckoutItemDto[]
```

It will generate:

1. `app/Dtos/CheckoutOrderDto.php`:

    ```php
    <?php

    namespace App\Dto;

    use Emsifa\Evo\Dto;
    use Emsifa\Evo\Types\ArrayOf;

    class CheckoutOrderDto extends Dto
    {
        #[ArrayOf(CheckoutItemDto::class)]
        public array $items;
    }
    ```
2. `app/Dtos/CheckoutItemDto.php` (if not exists):

    ```php
    <?php

    namespace App\Dto;

    use Emsifa\Evo\Dto;

    class CheckoutItemDto extends Dto
    {
    }
    ```

#### Default Casters

Here are some examples of Evo's default casters behave:

##### `BoolCaster`

| Property        | Original Value | Casted Value       |
|-----------------|----------------|--------------------|
| bool $property  | null           | false              |
| ?bool $property | null           | null               |
| bool $property  | true           | true               |
| bool $property  | false          | false              |
| bool $property  | "true"         | true               |
| bool $property  | "false"        | false              |
| bool $property  | 1              | true               |
| bool $property  | 0              | false              |
| bool $property  | "1"            | true               |
| bool $property  | "0"            | false              |
| bool $property  | 123            | CastErrorException |
| bool $property  | 123.45         | CastErrorException |
| bool $property  | "123"          | CastErrorException |
| bool $property  | "123.45"       | CastErrorException |
| bool $property  | "lorem ipsum"  | CastErrorException |
| bool $property  | []             | CastErrorException |
| bool $property  | [1,2,3]        | CastErrorException |
| bool $property  | stdClass       | CastErrorException |

##### `IntCaster`

| Property     | Original Value | Casted Value       |
|--------------|----------------|--------------------|
| int $number  | null           | 0                  |
| ?int $number | null           | null               |
| int $number  | 123            | 123                |
| int $number  | "123"          | 123                |
| int $number  | 123.45         | 123                |
| int $number  | "123.45"       | 123                |
| int $number  | "123.99"       | 123                |
| int $number  | true           | CastErrorException |
| int $number  | false          | CastErrorException |
| int $number  | "123-ipsum"    | CastErrorException |
| int $number  | "123-ipsum"    | CastErrorException |
| int $number  | []             | CastErrorException |
| int $number  | stdClass       | CastErrorException |

##### `FloatCaster`

| Property       | Original Value | Casted Value       |
|----------------|----------------|--------------------|
| float $number  | null           | 0                  |
| ?float $number | null           | null               |
| float $number  | 123            | 123.0              |
| float $number  | "123"          | 123.0              |
| float $number  | 123.45         | 123.45             |
| float $number  | "123.45"       | 123.45             |
| float $number  | "123.99"       | 123.99             |
| float $number  | true           | CastErrorException |
| float $number  | false          | CastErrorException |
| float $number  | "123-ipsum"    | CastErrorException |
| float $number  | "123-ipsum"    | CastErrorException |
| float $number  | []             | CastErrorException |
| float $number  | stdClass       | CastErrorException |

##### `FloatCaster`

| Property       | Original Value | Casted Value       |
|----------------|----------------|--------------------|
| float $number  | null           | 0                  |
| ?float $number | null           | null               |
| float $number  | 123            | 123.0              |
| float $number  | "123"          | 123.0              |
| float $number  | 123.45         | 123.45             |
| float $number  | "123.45"       | 123.45             |
| float $number  | "123.99"       | 123.99             |
| float $number  | true           | CastErrorException |
| float $number  | false          | CastErrorException |
| float $number  | "123-ipsum"    | CastErrorException |
| float $number  | "123-ipsum"    | CastErrorException |
| float $number  | []             | CastErrorException |
| float $number  | stdClass       | CastErrorException |

##### `StringCaster`

| Property     | Original Value | Casted Value       |
|--------------|----------------|--------------------|
| string $str  | null           | ""                 |
| ?string $str | null           | null               |
| string $str  | true           | "true"             |
| string $str  | false          | "false"            |
| string $str  | "123"          | "123"              |
| string $str  | 123            | "123"              |
| string $str  | 123.45         | "123.45"           |
| string $str  | 123.0          | "123"              |
| string $str  | Stringable     | __toString()       |
| string $str  | []             | CastErrorException |

##### `ArrayCaster`

`ArrayCaster` basically only accept array and `Illuminate\Contracts\Support\Arrayable` value, other than that it throws `Emsifa\Evo\Exceptions\CastErrorException`.

`ArrayCaster` will check `Emsifa\Evo\Types\ArrayOf` attribute to cast its items.

For example if you have property like this:

```php
#[ArrayOf('int')]
public array $numbers;
```

And inject it with value: 

```php
[1, "2", "3.0", null]
```

Evo will apply `int` caster to each items, so the result would be: 

```php
[1, 2, 3, 0]
```

In example above, if you inject it with value:

```php
[1, "2", "lorem-ipsum", null]
```

It will throw `Emsifa\Evo\Exceptions\CastErrorException`.

But sometimes you may want to treat it differently. Instead of thrown an error, you may want to skip the item, make it null, or just keep as is. That is why the second parameter of `ArrayOf` comes in handy.

You can use `ArrayOf::SKIP_ITEM`, `ArrayOf::NULL_ITEM`, `ArrayOf::KEEP_AS_IS` as second parameter of `ArrayOf` attribute.

For example, if you change the property in example above like this:

```php
#[ArrayOf('int', ArrayOf::SKIP_ITEM)]
public array $numbers;
```

And if you inject it with value:

```php
[1, "2", "lorem-ipsum", "4.2", "5"]
```

It will give you this result:

```php
[1, 2, 4, 5]
```

If you change second parameter to `ArrayOf::NULL_ITEM`, it would give you the result below:

```php
[1, 2, null, 4, 5]
```

Lastly if you change second parameter to `ArrayOf::KEEP_AS_IS`, it would give you this result:

```php
[1, 2, "lorem-ipsum", 4, 5]
```

##### `CollectionCaster`

Same like `ArrayCaster`, but it will apply `collect($result)` to transform the result into `Illuminate\Support\Collection` instance.

##### `DateTimeCaster`

`DateTimeCaster` will transform any string that is accepted by `date_create()` function to `DateTime` object.

For example:

```php
public DateTime $date;
```

It would transform string `"2010-01-02"`, `"2010-01-02 12:34:56"`, etc into `DateTime` instance.

If `date_create()` returns `null`, it would throws `Emsifa\Evo\Exceptions\CastErrorException`.

#### Creating Custom Type Caster

To create your own type caster, you have to create a class implementing `Emsifa\Evo\Contracts\Caster` interface.

In this example we will create a boolean caster. Also we will override Evo's default boolean caster with our custom caster.

First, let's create file `BoolCaster.php` in `app/Casters` directory. If you don't have `Casters` directory yet, just create it.

Then let's write this blank caster code:

```php
<?php

namespace App\Casters;

use Emsifa\Evo\Contracts\Caster;
use Emsifa\Evo\Exceptions\CastErrorException;
use ReflectionParameter;
use ReflectionProperty;

class BoolCaster implements Caster
{
    public function cast($value, ReflectionProperty | ReflectionParameter $prop): mixed
    {
        // ...
    }
}
```

Now we will put some logic to our `cast` method. For example in this custom `BoolCaster` we want to convert string `"1"`, `"true"`, `"on"`, `"yes"` to `true` and `"0"`, `"false"`, `"no"`, `"off"` to `false`. Also, we want to make sure if our property is nullable, and the value is `null`, we should returns `null`. Else we will throw `Emsifa\Evo\Exceptions\CastErrorException`.

```php
class BoolCaster implements Caster
{
    public function cast($value, ReflectionProperty | ReflectionParameter $prop): mixed
    {
        $nullable = optional($prop->getType())->allowsNull();
        $truthy = [true, "true", 1, "1", "yes", "on"];
        $falsy = [false, "false", 0, "0", "no", "off"];

        return match (true) {
            $nullable && is_null($value) => null,
            in_array($value, $truthy, true) => true,
            in_array($value, $falsy, true) => false,
            default => throw new CastErrorException("Cannot cast boolean from type: " . gettype($value)),
        };
    }
}
```

Now our `BoolCaster` is done. To use our `BoolCaster`, we can attach `Emsifa\Evo\Dtos\UseCaster` attribute to our DTO class.

In this example we will attach it to `LoginDto` class.

```php
<?php

namespace App\Dto;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Dtos\UseCaster;
use App\Casters\BoolCaster;

#[UseCaster('bool', BoolCaster::class)]
class LoginDto extends Dto
{
    public string $email;
    public string $password;
    public bool $remember;
}
```

Now, our `BoolCaster` will be applied to cast `bool $remember` property.

If you want to apply it to all of your DTO classes, you can create your own abstract DTO class to be used as a parent to all of your DTO classes.

For example, create your own `app/Dtos/Dto.php` file with code below:

```php
<?php

namespace App\Dto;

use Emsifa\Evo\Dto as EvoBaseDto;
use Emsifa\Evo\Dtos\UseCaster;
use App\Casters\BoolCaster;

#[UseCaster('bool', BoolCaster::class)]
abstract class Dto extends EvoBaseDto
{
}
```

Then in your DTO classes you just have to extends `App\Dtos\Dto` class instead of `Emsifa\Evo\Dto`.

### Validation

Evo will automatically validate request data to every parameters that is attached with attribute that implements `Emsifa\Evo\Contracts\RequestValidator` interface. Evo's built-in `Query`, `Header`, `Cookie`, `Param`, and `Body` attributes is doing validation because they are implementing that interface.

#### Validating Query, Header, Cookie, and Param

For `Query`, `Header`, `Cookie`, and `Param` attributes, by default they choose validation rules by looking for its parameter type. For example:

```php
#[Get]
public function index(
    #[Query] int $limit,
    #[Query] int $offset,
    #[Query] ?string $keyword = null,
)
{
}
```

Evo will apply `required|numeric` to `$limit` and `$offset`, also apply rule `nullable|string` rule to `$keyword`.

But you can also use your own validation rules by giving `$rules` to attribute. For example:

```php
#[Get]
public function index(
    #[Query(rules: 'required|numeric|min:10')] int $limit,
    #[Query(rules: 'required|numeric|min:0')] int $offset,
    #[Query] ?string $keyword = null,
)
{
}
```

Now `$limit` and `$offset` parameters will use your defined rules instead of using the default rules.

#### Validating Body

For `Body` attribute. It will scan its DTO properties, and choose rules for each properties by looking for the property type and finding `Illuminate\Contracts\Validation\Rule` attributes attached to it.

For example, if you have controller like this:

```php
#[Post('checkout')]
public function checkout(#[Body] CheckoutOrderDto $dto)
{
    // ...
}
```

Where `CheckoutOrderDto` has properties like this:

```php
<?php

namespace App\Dto;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Rules;

class CheckoutOrderDto extends Dto
{
    #[Rules\Required]
    #[Rules\Numeric]
    public string $phone;

    #[Rules\Required]
    public string $address;

    #[Rules\Required]
    #[Rules\Numeric]
    #[Rules\Size(11)]
    public string $subdistrict_id;

    #[Rules\Required]
    #[Rules\Numeric]
    #[Rules\Size(5)]
    public string $postal_code;

    #[ArrayOf(CheckoutOrderItemDto::class)]
    public array $items;
}
```

And `CheckoutOrderItemDto` like this:

```php
<?php

namespace App\Dto;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Rules;

class CheckoutOrderItemDto extends Dto
{
    #[Rules\Required]
    #[Rules\Exists('products', 'id')]
    public string $product_id;

    #[Rules\Required]
    #[Rules\Numeric]
    public int $qty;
}
```

The `Body` attribute will do this following validation for you:

```php
Validator::make($data, [
    'phone' => 'required|string|numeric',
    'address' => 'required|string',
    'subdistrict_id' => 'required|string|numeric|size:11',
    'postal_code' => 'required|string|numeric|size:5',
    'items' => 'required|array',
    'items.*.product_id' => 'required|string|exists:product,id',
    'items.*.qty' => 'required|numeric',
]);
```

##### Create Your Own Validation Rule

Evo's validation rule is basically a class implementing `Illuminate\Contracts\Validation\Rule` interface and has `Attribute` attribute attached on it.

For example, we will create `Bit` rule that only accepts string with "0" and "1" characters.

First, create `app/Rules/Bit.php` file.

Then write following code below:

```php
<?php

namespace App\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Bit implements Rule
{
    public function __construct(protected string $message = '')
    {
    }

    public function passes($attribute, $value)
    {
        return is_string($value) && preg_match("/^(0|1)+$/", $value);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.bit") ?: "Invalid bit string";
    }
}
```

That's it! now you can attach it to your DTO's property like this:

```php
<?php

namespace App\Dto;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Rules;
use App\Rules\Bit;

class MyDto extends Dto
{
    #[Rules\Required]
    #[Bit]
    public string $bit_string;
}
```

### Using Response

Evo provide custom response class `Emsifa\Evo\Http\Response\JsonResponse` and `Emsifa\Evo\Http\Response\ViewResponse` that is inherit from `Emsifa\Evo\Dto` class.
They are all implements `Illuminate\Contracts\Support\Responsable`, so that they can be transformed to HTTP response.

You can extend them to your response classes then use your response classes as return type of your controller action to get these benefits below:

* Easier to identify how the response data should be.
* It prevents you to send wrong data types. Eg: integer as string, null to non-nullable property, etc.
* It makes Evo knows how to generate your endpoint into OpenAPI.
* It makes Evo knows how to mock the response.

#### Generate Response Class

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

#### Using Response Class

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

#### Using View Response

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

#### Define Response Status

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

#### Using Json Template

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

To do this, we have to create a JSON response class that implements `Emsifa\Evo\Contracts\JsonTemplate` as a wrapper to all of your JSON response classes.

Let's create file `app/Http/Responses/SuccessJsonTemplate.php` with following code:

```php
<?php

namespace App\Http\Responses;

use Emsifa\Evo\Contracts\JsonData;
use Emsifa\Evo\Contracts\JsonTemplate as JsonTemplateContract;
use Emsifa\Evo\Http\Response\JsonResponse;

class SuccessJsonTemplate implements JsonTemplateContract
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

Now you can use this template using `Emsifa\Evo\Http\Response\UseJsonTemplate` like code below:

```php
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

### Swagger UI

Swagger UI is a web based application to visualize and interacts with API's resources without having any of the implementation logic in place. It reads OpenAPI schema to display and interact with API endpoints.

Evo can reflect your code and generate OpenAPI schema on-the-fly, so you can display Swagger UI with minimal configuration.

In this section we will guide you on how to use Swagger UI with Evo.

#### Publishing Assets Files

First, you have to publish assets files that will be used by Swagger UI page.

```php
php artisan vendor:publish --tag=evo-assets
```

#### Register Swagger Routes

Then, you have to register two routes. First route is for displaying Swagger UI and second route is for rendering OpenAPI specification as JSON.

To do that, you can simply add this line to your `routes/web.php`:

```php
Evo::swagger('/docs');
```

Now if you run `php artisan route:list`, you should see `GET /docs` and `GET /docs/openapi` routes there.

You can check it by running your app with `php artisan serve`, then in your browser, open URL `http://localhost:8000/docs`.

You should see Swagger UI page with no endpoints yet.

#### Add an Example Endpoint

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

#### Configuring OpenAPI

To configure OpenAPI, Evo provides some attributes modifier that modify OpenAPI schema. Also Evo have configuration file to modify some information in OpenAPI.

To configure info with configuration file. First you have to publish configuration file by running following command:

```bash
php artisan vendor:publish --tag=evo-config
```

It will create `config/evo.php` in your project directory. Take a look, and modify it as you want.

##### `Example` Attribute

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

##### `Summary` Attribute

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

##### `Description` Attribute

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

##### Create Custom OpenAPI Modifiers

To use create custom OpenAPI modifiers, Evo provides some interfaces that you can use to your custom attribute.

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

### Mocking API

Mocking API is a feature that allows your controller to send response with fake data.

When developing REST API with a team consisting of Back-end and Front-end Developers. Creating an API implementation could takes time. Sometimes it makes Front-end developer have to wait Back-end Developer to finish the implementation. This will make development time less efficient.

To make development time more efficient. We can use Mocking API so Front-end Developer can consume our API without waiting for real implementation to be done.

To do that, in Evo you can simply attach `Emsifa\Evo\Http\Response\Mock` attribute to your controller method. So when user call that endpoint, Evo will prevent its controller to be executed, instead Evo will read its return type, create return type instance and fill its properties with fake data, finally Evo will respond that fake instance.

#### Using `Mock` Attribute

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

#### Using Specific Faker

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


#### Create Custom Faker

If you want to use your own custom faker formatter, you can create a class implementing `Emsifa\Evo\Contracts\ValueFaker`.

For example, we will create `MealsFaker` that will resulting a random meals name.

First, create `app/Fakers/MealsFaker.php` file.

Fill it with code below:

```php
<?php

namespace App\Fakers;

use Emsifa\Evo\Contracts\ValueFaker;
use Faker\Generator;
use ReflectionProperty;

class MealsFaker implements ValueFaker
{
    public function generateFakeValue(Generator $faker, ReflectionProperty $property): mixed
    {
        return $faker->randomElement([
            "Kebab",
            "Ramen",
            "Fried Chicken",
            "Pizza",
            "Sandwich"
        ]);
    }
}
```

Now to use it, we will add new `$meal` property to our `PostSomethingResponse`, so our `PostSomethingResponse.php` would be like this:

```php
<?php

namespace App\Http\Responses;

use Emsifa\Evo\Dtos\UseFaker;
use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Fakers\MealsFaker;

#[Description("Post something succeed")]
class PostSomethingResponse extends JsonResponse
{
    public int $number;

    #[UseFaker("paragraph", 5)]
    public string $message;

    #[UseFaker(MealsFaker::class)]
    public string $meal;
}
```

Now if you execute your endpoint again, you will get
`meal` data that is could be "Kebab", "Ramen", "Fried Chicken", "Pizza", or "Sandwich".

#### `FakesCount` Attribute

`FakesCount` attribute is used to define how many fake items you want to be generated into your `array` property.

For example, we will add `$numbers` property to `PostSomethingResponse` that is attached with `FakesCount` attribute:

```php
<?php

namespace App\Http\Responses;

use Emsifa\Evo\Dtos\UseFaker;
use Emsifa\Evo\Dtos\FakesCount;
use Emsifa\Evo\Types\ArrayOf;
use Emsifa\Evo\Http\Response\JsonResponse;
use Emsifa\Fakers\MealsFaker;

#[Description("Post something succeed")]
class PostSomethingResponse extends JsonResponse
{
    public int $number;

    #[UseFaker("paragraph", 5)]
    public string $message;

    #[UseFaker(MealsFaker::class)]
    public string $meal;
    
    #[ArrayOf('int')]
    #[FakesCount(5)]
    public array $numbers;
}
```

It will generate 5 random numbers when you execute your mock API.

#### Ignoring Mock

In local development environment, you may not want your API to be mocked. Instead of remove `Mock` attribute temporarily, you can just add `IGNORE_MOCK=true` in your `.env` file.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Muhammad Syifa](https://github.com/emsifa)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
