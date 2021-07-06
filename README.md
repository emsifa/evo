# Laravel Evo

[![Latest Version on Packagist](https://img.shields.io/packagist/v/emsifa/evo.svg?style=flat-square)](https://packagist.org/packages/emsifa/evo)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/emsifa/evo/run-tests?label=tests)](https://github.com/emsifa/evo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/emsifa/evo/Check%20&%20fix%20styling?label=code%20style)](https://github.com/emsifa/evo/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/emsifa/evo.svg?style=flat-square)](https://packagist.org/packages/emsifa/evo)

---
Laravel Evo is package to evolve your Laravel code into something like this:

```php
#[UsePrefix('users')]
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

## But Why?

By defining input and output types in head part of a function, it triggers your brain to specifies input and output before writing its logic.
So when it comes to write logic, you know exactly what you have, where it comes, and what to return.

Also, by defining input and output type like this, not only you and your teammate would easily read the specifications. Machines too.

That is why, when you add this line to `routes/web.php`:

```php
Emsifa\Evo\EvoFacade::swagger('/docs');
```

Evo are able to read your specifications, and display this stuff for you:

> swagger screenshots here


## Installation

You can install the package via composer:

```bash
composer require emsifa/evo
```

## Usage

To be able to use Laravel Controller in Evo way, you have to register route with `EvoFacade::routes` method like below:

```php
// routes/web.php or routes/api.php

use Emsifa\Evo\EvoFacade as Evo;

Evo::routes(App\Http\Controllers\UserController::class);
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
