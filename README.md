![Logo](https://raw.githubusercontent.com/emsifa/evo/main/logo.svg)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/emsifa/evo.svg?style=flat-square)](https://packagist.org/packages/emsifa/evo)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/emsifa/evo/run-tests?label=tests)](https://github.com/emsifa/evo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/emsifa/evo/Check%20&%20fix%20styling?label=code%20style)](https://github.com/emsifa/evo/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/emsifa/evo.svg?style=flat-square)](https://packagist.org/packages/emsifa/evo)

---
Laravel Evo is package to change the way you write Laravel app into something like this:

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
        #[Body] CreateUserDTO $data
    ): StoreUserResponse
    {
        // your logic goes here
    }
    
    #[Put('{id}')]
    public function update(
        #[Param] int $id,
        #[Body] UpdateUserDTO $data,
    ): UpdateUserResponse
    {
        // your logic goes here
    }
}
```

## Motivation

Defining input and output types in head part of a function will trigger your brain to specifies input and output before writing its logic.
So when it comes to write logic, you know exactly what you have, where it comes, and what to return.

Also, by defining input and output type like this, not only you and your teammate would easily read the specifications. Machines too.
That is why Evo can provide some amazing features such as auto validation, auto casting, live swagger documentation, mocking API, etc.

## Features

* [x] Inject request data (Header, Param, Cookie, Body, Query) into parameters using attribute.
* [x] Applying middleware using attribute. 
* [x] Route prefixing using attribute. 
* [x] Automatic type casting.
* [x] Automatic type validation.
* [x] Define validation rules directly in DTO properties using attribute.
* [x] Custom value caster.
* [x] Generate DTO file.
* [x] Generate Response file.
* [x] Generate OpenAPI file.
* [x] Display Swagger page.
* [x] Mocking API.


## Installation

You can install the package via composer:

```bash
composer require emsifa/evo
```

## Usage

### Routing

#### Register Route

To be able to use Laravel Controller in Evo way, you have to register route with `EvoFacade::routes` method like below:

```php
// routes/web.php or routes/api.php

use Emsifa\Evo\EvoFacade as Evo;

Evo::routes(App\Http\Controllers\UserController::class);
```

Then in your `UserController`, you can attach route attribute such as `Get`, `Post`, `Put`, `Patch`, `Delete` like example below:

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

In Evo, you can access request value by attaching attributes such as `Query`, `Cookie`, `Header`, `Param`, `Body`, etc to your method parameters. Then Evo will automatically inject corresponding value to your parameters. Evo will also automatically validate and cast the value according to parameter type and definition.

Before using those attributes, make sure you have import their full class names.

```php
use Emsifa\Evo\Http\Param;
use Emsifa\Evo\Http\Query;
use Emsifa\Evo\Http\Header;
use Emsifa\Evo\Http\Cookie;
use Emsifa\Evo\Http\Body;
```

#### `Query` Attribute

`Query` attribute used to get value from HTTP request query.

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

If you want to make it optional, you can just give default value to it. For example:

```php
#[Get]
public function index(#[Query('q')] ?string $keyword = null)
{
    // ...
} 
```

#### `Param` Attribute

`Param` attribute used to get URI parameter value.

```php
#[Get('users/{id}')]
public function index(#[Param] int $id)
{
    // ...
} 
```

Like `Query` attribute before, Evo will do validation, type casting, and inject it to the `$id` parameter. 

#### `Header` Attribute

`Header` attribute used to get request header value.

```php
#[Get('users/{id}')]
public function index(#[Header('user-agent')] string $userAgent)
{
    // ...
} 
```

In example above, Evo will get `user-agent` header value and inject it to the `$userAgent` parameter.


#### `Cookie` Attribute

`Cookie` attribute used to get cookie value from HTTP request.

```php
#[Get('users/{id}')]
public function index(#[Cookie] string $token)
{
    // ...
} 
```

In example above, Evo will get `token` cookie value and inject it to `$token` parameter.


#### `Body` Attribute

`Body` attribute is used to get value from HTTP request body.
To use `Body` attribute you have to use `DTO` class as the type of your parameter.
You can create `DTO` class by using `evo:make-dto` command.

In this example, we will inject request body value as `RegisterDTO` instance into `register` method.

First, we have to generate `RegisterDTO` class with command:

```bash
php artisan evo:make-dto RegisterDTO name:string email:string password:string password_confirmation:string
```

Then you will get `app/DTO/RegisterDTO.php` file with code like this:

```php
<?php

namespace App\DTO;

use Emsifa\Evo\DTO;

class RegisterDTO extends DTO
{
    public string $name;
    public string $email;
    public string $password;
    public string $password_confirmation;
}
```

Now you may want to add an extra validation to each properties. You can do that by attaching rules attribute like an example below:


```php
<?php

namespace App\DTO;

use Emsifa\Evo\DTO;
use Emsifa\Evo\Rules;

class RegisterDTO extends DTO
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

Example above will do similar validation like below:

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

After defining validation rules to properties, you can inject `RegisterDTO` instance to your method like any other attributes before, but with `Body` attribute.

```php
#[Post('register')]
public function register(#[Body] RegisterDTO $dto)
{
    // ...
}
```

And there you go. Same like any other attributes. Evo will validate the values, resolving `RegisterDTO` instance, and inject it to `$dto` parameter.

#### Uploaded File

You can get uploaded files in two ways:

1. Using DTO class and `Body` attribute.
2. Using `File` attribute.

##### 1. Getting uploaded file using DTO and `Body` attribute

To get uploaded file using DTO, you can just add property with type `Illuminate\Http\UploadedFile`.

```php
<?php

namespace App\DTO;

use Emsifa\Evo\DTO;
use Emsifa\Evo\Rules;
use Illuminate\Http\UploadedFile;

class UpdateProfileDTO extends DTO
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
    #[Body] RegisterDTO $dto,
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
use ReflectionParameter;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PARAMETER)]
class JwtToken implements RequestGetter
{
    public function getRequestValue(Request $request, ReflectionParameter | ReflectionProperty $reflection): mixed
    {
        $tokenFromHeader = $request->header("authorization");
        $tokenFromCookie = $request->cookie("token");

        return $tokenFromHeader
            ? Str::after($tokenFromHeader, "Bearer ")
            : $tokenFromCookie;
    }
}
```

Now you can use it like any other request attributes like this:

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
use Illuminate\Support\Facades\Validator;
use ReflectionParameter;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PARAMETER)]
class JwtToken implements RequestGetter, RequestValidator
{
    public function getRequestValue(Request $request, ReflectionParameter | ReflectionProperty $reflection): mixed
    {
        $tokenFromHeader = $request->header("authorization");
        $tokenFromCookie = $request->cookie("token");

        return $tokenFromHeader
            ? Str::after($tokenFromHeader, "Bearer ")
            : $tokenFromCookie;
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

DTO (Data Transfer Object) is basically an object that have some declared properties in it. DTO used to carry data between processes. In typical PHP application, we often store data as associative array or as `stdClass`. The downside of storing data as associative array or `stdClass` is we don't really know what is inside and what type it is. If we are not carefully to check it, it could cause some security problem to our application.

By replacing them with DTO, we, Text Editor, and IDE know exactly what it is, what properties in it, the type of each properties, etc.

But creating DTO instance with PHP native way sometimes can be quite annoying. You have to create an instance, set its properties value one by one, also you have to cast its type properly.

That is why Evo provide `Emsifa\Evo\DTO` class that comes with some useful utilities.

In the [`Body` Attribute](#body-attribute) section, we are creating `RegisterDTO` class that will be injected using `Body` attribute. Yes, the validation process does comes from `Body` attribute, but the ability to create `RegisterDTO` instance with the correct types for its properties is comes from its parent class: `Emsifa\Evo\DTO`.

`Emsifa\Evo\DTO` doing type casting by looking for type casters attached to it. If you look at the source of `Emsifa\Evo\DTO`, you will see something like this:

```php
#[UseCaster('int', IntCaster::class)]
#[UseCaster('float', FloatCaster::class)]
#[UseCaster('string', StringCaster::class)]
#[UseCaster('bool', BoolCaster::class)]
#[UseCaster('array', ArrayCaster::class)]
#[UseCaster(DateTime::class, DateTimeCaster::class)]
#[UseCaster(Collection::class, CollectionCaster::class)]
abstract class DTO implements Arrayable
{
    ...
}
```

In code above, `UseCaster` is used to tell your DTO what caster class to be used to cast each types.

In this section we will explore how to generate DTO file, how default casters behave, and how to create and overriding default caster.

#### Generating DTO File

We can generate DTO file using `evo:make-dto` command. Below is the signature of the command:

```bash
php artisan evo:make-dto {filename} {...properties}
```

Argument `filename` is required and `properties` are optional.

For example, to create `LoginDTO` that have `string $email` and `string $password` properties. We should run:

```bash
php artisan evo:make-dto LoginDTO email:string password:string
```

It will generate `app/DTO/LoginDTO.php` with code:

```php
<?php

namespace App\DTO;

use Emsifa\Evo\DTO;

class LoginDTO extends DTO
{
    public string $email;
    public string $password;
}

```

You can also generate nested object by giving the name of the object for the type. For example:

```bash
php artisan evo:make-dto SaveProfileDTO name:string contact:ContactDTO address:AddressDTO
```

It will generate 3 files below: 

1. `app/DTO/SaveProfileDTO.php`:
    ```php
    <?php

    namespace App\DTO;

    use Emsifa\Evo\DTO;

    class SaveProfileDTO extends DTO
    {
        public string $name;
        public ContactDTO $contact;
        public AddressDTO $contact;
    }
    ```
2. `app/DTO/ContactDTO.php` (if not exists):
    ```php
    <?php

    namespace App\DTO;

    use Emsifa\Evo\DTO;

    class ContactDTO extends DTO
    {
    }
    ```

3. `app/DTO/AddressDTO.php` (if not exists):
    ```php
    <?php

    namespace App\DTO;

    use Emsifa\Evo\DTO;

    class AddressDTO extends DTO
    {
    }
    ```

You can also generate typed array by adding `[]` sign after type name. For example:

```bash
php artisan evo:make-dto CheckoutOrderDTO items:CheckoutItemDTO[]
```

It will generate:

1. `app/DTO/CheckoutOrderDTO.php`:

    ```php
    <?php

    namespace App\DTO;

    use Emsifa\Evo\DTO;
    use Emsifa\Evo\Types\ArrayOf;

    class CheckoutOrderDTO extends DTO
    {
        #[ArrayOf(CheckoutItemDTO::class)]
        public array $items;
    }
    ```
2. `app/DTO/CheckoutItemDTO.php` (if not exists):

    ```php
    <?php

    namespace App\DTO;

    use Emsifa\Evo\DTO;

    class CheckoutItemDTO extends DTO
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

`ArrayCaster` basically only accept array and `Illuminate\Contracts\Support\Arrayable` value, otherwise it throws `Emsifa\Evo\Exceptions\CastErrorException`.

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

For example, if you change your property definition to this:

```php
#[ArrayOf('int', ArrayOf::SKIP_ITEM)]
public array $numbers;
```

If you inject it with value:

```php
[1, "2", "lorem-ipsum", "4.2", "5"]
```

It will give you result:

```php
[1, 2, 4, 5]
```

If you change second parameter to `ArrayOf::NULL_ITEM`, it would give you result below:

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

Other than that, it throws `Emsifa\Evo\Exceptions\CastErrorException`.

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

Now we will put some logic to our `cast` method. For example in this custom `BoolCaster` we want to convert string "1", "true", "on", "yes" to `true` and "0", "false", "no", "off" to `false`. Also, we want to make sure if our property is nullable, and the value is `null`, we should returns `null`. Other than that we will throw `Emsifa\Evo\Exceptions\CastErrorException`.

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
            in_array($value, $truthy) => true,
            in_array($value, $falsy) => false,
            default => throw new CastErrorException("Cannot cast boolean from type: " . gettype($value)),
        };
    }
}
```

Now our `BoolCaster` is done. To use our `BoolCaster`, we can attach `Emsifa\Evo\DTO\UseCaster` attribute to our DTO class.

In this example we will attach it to `LoginDTO` class.

```php
<?php

namespace App\DTO;

use Emsifa\Evo\DTO;
use Emsifa\Evo\DTO\UseCaster;
use App\Casters\BoolCaster;

#[UseCaster('bool', BoolCaster::class)]
class LoginDTO extends DTO
{
    public string $email;
    public string $password;
    public bool $remember;
}
```

Now, our `BoolCaster` will be applied to cast `bool $remember` property.

If you want to apply it to all of your DTO classes, you can create your own abstract DTO class to be used as a parent of your DTO classes.

For example, create your own `app/DTO/DTO.php` file with code below:

```php
<?php

namespace App\DTO;

use Emsifa\Evo\DTO as EvoBaseDTO;
use Emsifa\Evo\DTO\UseCaster;
use App\Casters\BoolCaster;

#[UseCaster('bool', BoolCaster::class)]
abstract class DTO extends EvoBaseDTO
{
}
```

Then in your DTO classes you just have to extends `App\DTO\DTO` class instead of `Emsifa\Evo\DTO`.


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
