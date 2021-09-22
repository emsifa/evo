---
sidebar_position: 3
---

# Request

In Evo, you can access request value by attaching request attributes such as `Query`, `Cookie`, `Header`, `Param`, `Body`, etc to your method parameters. Then Evo will automatically inject corresponding value to attached parameter. Evo will also automatically validate and cast the value according to parameter type and definition.

Before using those attributes, make sure you have import their full class names.

```php
use Emsifa\Evo\Http\Param;
use Emsifa\Evo\Http\Query;
use Emsifa\Evo\Http\Header;
use Emsifa\Evo\Http\Cookie;
use Emsifa\Evo\Http\Body;
```

## `Query` Attribute

`Query` attribute is used to get a query value from HTTP request.

```php {2}
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

```php {2}
#[Get]
public function index(#[Query('p')] int $page)
{
    // ...
} 
```

In example above Evo will get `p` query value and inject it to `$page` parameter.

If you want to make it optional, you can just give default value to the parameter. For example:

```php {2}
#[Get]
public function index(#[Query('q')] int $page = 1)
{
    // ...
} 
```

Or you can also make it nullable, for example:

```php {2}
#[Get]
public function index(#[Query('q')] ?int $page)
{
    // ...
}
```

## `Param` Attribute

`Param` attribute is used to get URI parameter value.

```php {2}
#[Get('users/{id}')]
public function index(#[Param] int $id)
{
    // ...
} 
```

Like `Query` attribute before, Evo will do validation, type casting, and inject the value to the `$id` parameter. 

## `Header` Attribute

`Header` attribute is used to get a header value from HTTP request.

```php {2}
#[Get('users/{id}')]
public function index(#[Header('user-agent')] string $userAgent)
{
    // ...
} 
```

In example above, Evo will get `user-agent` header value and inject it to the `$userAgent` parameter.


## `Cookie` Attribute

`Cookie` attribute is used to get a cookie value from HTTP request.

```php {2}
#[Get('users/{id}')]
public function index(#[Cookie] string $token)
{
    // ...
} 
```

In example above, Evo will get `token` cookie value and inject it to the `$token` parameter.


## `Body` Attribute

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


```php {6,10,13-15,18,19,22,23}
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

```php {2}
#[Post('register')]
public function register(#[Body] RegisterDto $dto)
{
    // ...
}
```

And there you go. Same like any other attributes. Evo will validate the values, resolving `RegisterDto` instance, and inject it to `$dto` parameter.

## Uploaded File

You can get uploaded files in two ways below:

1. Using DTO class and `Body` attribute.
2. Using `File` attribute.

### 1. Getting uploaded file using DTO and `Body` attribute

To get uploaded file using DTO, you can just add a property with type `Illuminate\Http\UploadedFile`.

```php {7,16}
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

### 2. Getting uploaded file using `File` attribute

If you want directly inject uploaded file instance to your controller method, you can use `File` attribute like an example below:

```php {4}
#[Post('register')]
public function register(
    #[Body] RegisterDto $dto,
    #[File(rules: 'required|mimes:jpeg,png|image')] UploadedFile $avatar,
)
{
    // ...
}
```

## `LoggedUser` Attribute

`LoggedUser` attribute is used to get current logged user instance from `request()->user()`.
You can use it like any other request attributes before, just make sure if its optional, you have to make it nullable and set default value to null.

```php {3}
public function show(
    #[Param] int $id,
    #[LoggedUser] ?User $user = null,
)
{
    // ...
}
```

## Create Custom Request Attribute

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


```php {7,10,15,27-38}
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