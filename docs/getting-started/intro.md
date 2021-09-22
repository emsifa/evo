---
sidebar_position: 1
---

# Introduction

[![Latest Version on Packagist](https://img.shields.io/packagist/v/emsifa/evo.svg?style=flat-square)](https://packagist.org/packages/emsifa/evo)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/emsifa/evo/run-tests?label=tests&style=flat-square)](https://github.com/emsifa/evo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Coverage Status](https://img.shields.io/codecov/c/github/emsifa/evo?style=flat-square&token=6DJ6S9MOGO)](https://app.codecov.io/github/emsifa/evo)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/emsifa/evo/Check%20&%20fix%20styling?label=code%20style&style=flat-square)](https://github.com/emsifa/evo/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/emsifa/evo.svg?style=flat-square)](https://packagist.org/packages/emsifa/evo)

Evo is a Laravel package that leverages PHP 8 features. It change the way you write Laravel app into something like this:

```php
#[RoutePrefix('users')]
#[RouteMiddleware('auth')]
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
