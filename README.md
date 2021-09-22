<div align="center">
    <img alt="Logo" src="https://raw.githubusercontent.com/emsifa/evo/main/logo.svg" height="100px"/>
</div>

<div align="center">
    
[![Latest Version on Packagist](https://img.shields.io/packagist/v/emsifa/evo.svg?style=flat-square)](https://packagist.org/packages/emsifa/evo)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/emsifa/evo/run-tests?label=tests&style=flat-square)](https://github.com/emsifa/evo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Coverage Status](https://img.shields.io/codecov/c/github/emsifa/evo?style=flat-square&token=6DJ6S9MOGO)](https://app.codecov.io/github/emsifa/evo)
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

* [x] Register routes using attributes.
* [x] Apply middleware using attribute. 
* [x] Route prefixing using attribute. 
* [x] Inject request data (Header, Param, Cookie, Body, Query) into parameters using attribute.
* [x] Automatic type casting.
* [x] Automatic type validation.
* [x] Define validation rules directly in DTO properties using attribute.
* [x] Custom value caster.
* [x] Generate DTO file.
* [x] Generate Response file.
* [x] Generate Swagger UI and OpenAPI file.
* [x] Mocking API.


## Installation

> Evo currently is still in the development, it could have some breaking changes before the final release.

You can install the package via composer:

```bash
composer require emsifa/evo:dev-main
```

## Documentation

See [https://www.emsifa.com/evo](https://www.emsifa.com/evo/).


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
