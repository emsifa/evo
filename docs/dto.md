---
sidebar_position: 4
---

# DTO

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

## Generating DTO File

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

## Default Casters

Here are some examples of Evo's default casters behave:

### `BoolCaster`

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

### `IntCaster`

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

### `FloatCaster`

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

### `FloatCaster`

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

### `StringCaster`

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

### `ArrayCaster`

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

### `CollectionCaster`

Same like `ArrayCaster`, but it will apply `collect($result)` to transform the result into `Illuminate\Support\Collection` instance.

### `DateTimeCaster`

`DateTimeCaster` will transform any string that is accepted by `date_create()` function to `DateTime` object.

For example:

```php
public DateTime $date;
```

It would transform string `"2010-01-02"`, `"2010-01-02 12:34:56"`, etc into `DateTime` instance.

If `date_create()` returns `null`, it would throws `Emsifa\Evo\Exceptions\CastErrorException`.

## Creating Custom Type Caster

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
