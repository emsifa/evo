---
sidebar_position: 2
---

# Routing

## Register Route

To be able to use Laravel Controller in Evo's way, you have to register route with `EvoFacade::routes` method like following code below:

```php
// routes/web.php or routes/api.php

use Emsifa\Evo\EvoFacade as Evo;

Evo::routes(App\Http\Controllers\UserController::class);
```

Then in your `UserController`, you can attach route attribute such as `Get`, `Post`, `Put`, `Patch`, `Delete` like an example below:

```php {6-9,13,19,25,31,37}
<?php

namespace App\Http\Controllers;

use Emsifa\Evo\Http\Param;
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
    public function show(#[Param] int $id)
    {
        // ...
    }
    
    #[Put('users/{id}')]
    public function update(#[Param] int $id)
    {
        // ...
    }
    
    #[Delete('users/{id}')]
    public function destroy(#[Param] int $id)
    {
        // ...
    }
}
```

## Route Prefixing

If you want to apply route prefix to every routes in a controller, you can attach `RoutePrefix` attribute to  your controller class.

```php {6,12}
<?php

namespace App\Http\Controllers;

use Emsifa\Evo\Http\Param;
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
    public function show(#[Param] int $id)
    {
        // ...
    }
    
    #[Put('{id}')]
    public function update(#[Param] int $id)
    {
        // ...
    }
    
    #[Delete('{id}')]
    public function destroy(#[Param] int $id)
    {
        // ...
    }
}
```


## Route Naming

To set route name, you can set `$name` parameter in route attributes.

```php {12,18}
<?php

namespace App\Http\Controllers;

use Emsifa\Evo\Http\Param;
use Emsifa\Evo\Route\RoutePrefix;
use Emsifa\Evo\Route\Get;

#[RoutePrefix('users')]
class UserController extends Controller
{
    #[Get(name: 'users.index')]
    public function index()
    {
        // ...
    }
    
    #[Get('{id}', name: 'users.show')]
    public function show(#[Param] int $id)
    {
        // ...
    }
}
```

If you want to set name prefix to every routes in a controller, you can use `Emsifa\Evo\Route\RouteName` attribute to controller class like an example below:

```php {7,11}
<?php

namespace App\Http\Controllers;

use Emsifa\Evo\Http\Param;
use Emsifa\Evo\Route\RoutePrefix;
use Emsifa\Evo\Route\RouteName;
use Emsifa\Evo\Route\Get;

#[RoutePrefix('users')]
#[RouteName('users')]
class UserController extends Controller
{
    #[Get(name: 'index')]
    public function index()
    {
        // ...
    }
    
    #[Get('{id}', name: 'show')]
    public function show(#[Param] int $id)
    {
        // ...
    }
}
```

By default `RouteName` attribute will add "." separator, so example above will add route with names:

* `"users.index"`
* `"users.show"`

If you want to use different separator, you can set second parameter. For example if you change example above with `RouteName('users', ':')`, you will have "users:index" and "users:show" route names instead.

## Applying Middleware

Every route attributes have `$middleware` parameter that you can set to apply middleware. Here is some examples:

```php {11,17}
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

```php {6,10}
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