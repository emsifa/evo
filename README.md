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

* [x] Inject request data (Header, Param, Cookie, Body, Query) into arguments using attribute.
* [x] Applying middleware using attribute. 
* [x] Route prefixing using attribute. 
* [x] Automatic type casting. Evo can automatically cast date string input into `DateTime` object, file into Laravel `UploadedFile` object, etc.
* [x] Automatic type validation. When you define `#[Query] int $limit`, Evo will reject request if limit query is not numeric.
* [x] Define validation rules directly in DTO using attribute.
* [x] Custom value casters.
* [x] Generate DTO file.
* [x] Generate Response file.
* [x] Generate OpenAPI file.
* [x] Display Swagger page.


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

Every route attribute has `$middleware` parameter that you can set to apply middleware. Here is some example:

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

If you want to apply same middleware to all routes in a controller, you can attach `RouteMiddleware` to your controller class like example below:

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

Before using those attributes, make sure you have use these full class names.

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

In example above, Evo will get `user-agent` header value and inject it to `$userAgent` parameter.


#### `Cookie` Attribute

`Cookie` attribute used to get request cookie value.

```php
#[Get('users/{id}')]
public function index(#[Cookie] string $token)
{
    // ...
} 
```

In example above, Evo will get `token` cookie value and inject it to `$token` parameter.


#### `Body` Attribute

`Body` attribute used to get value from request body.
To use `Body` attribute you have to use `DTO` class as the type of your parameter.
You can create `DTO` class by using `evo:make-dto` command.

In this example, we will inject `RegisterDTO` to `register` method.

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

Now you may want to add some validation to it. You can do that by attaching rules attribute like an example below:


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
You can use it like any other request attributes before, just make sure if its optional, you have to make it nullable and give it null default value.

```php
public function show(
    #[Param] int $id,
    #[LoggedUser] ?User $user = null,
)
{
    // ...
}
```

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
