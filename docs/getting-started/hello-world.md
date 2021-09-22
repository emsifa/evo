---
sidebar_position: 3
---

# Hello World

In this section we will show you how to make hello world in Evo's way.

## 1. Create Controller

To create controller, you can use Laravel `make:controller` command as usual.

```php
php artisan make:controller HelloController
```

## 2. Register Route

In Evo, we register routes directly in controller class using attribute.
In this example, we will register `GET /hello` route in our `HelloController` that is just generated before.

To do that, we will create method `hello`, and attach `Emsifa\Evo\Route\Get` attribute on it.

Modify your `HelloController.php` like following code below:

```php {5,9,10-13}
<?php

namespace App\Http\Controllers;

use Emsifa\Evo\Route\Get;

class HelloController extends Controller
{
    #[Get('hello')]
    public function hello()
    {
        return "Hello, World!";
    }
}
```

## 3. Register Controller

To make our `GET /hello` route works, 
we need to register `HelloController` using `EvoFacade::routes()` 
method like following code below:

```php
use Emsifa\Evo\EvoFacade as Evo;
use App\Http\Controllers\HelloController;

Evo::routes(HelloController::class);
```

Code above will scan routes in `App\Http\Controllers\HelloController` class and register them.

Now, when you run `php artisan route:list`, you will see `GET /hello` route there:

```bash
+--------+----------+-----------------+-------------+----------------------------------------------+-------------------------------------------+
| Domain | Method   | URI             | Name        | Action                                       | Middleware                                |
+--------+----------+-----------------+-------------+----------------------------------------------+-------------------------------------------+
|        | GET|HEAD | hello           |             | App\Http\Controllers\HelloController@hello   |                                           |
+--------+----------+-----------------+-------------+----------------------------------------------+-------------------------------------------+
```

## 4. Run Application

Our Hello World app now is done. You can run your app using `php artisan serve`, 
then open `http://localhost:8000/hello` in your browser.

If all correct, you will see "Hello, World!" message there. 