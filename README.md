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
