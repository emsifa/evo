---
sidebar_position: 5
---

# Validation

Evo will automatically validate request data to every parameters that is attached with attribute that implements `Emsifa\Evo\Contracts\RequestValidator` interface. Evo's built-in `Query`, `Header`, `Cookie`, `Param`, and `Body` attributes is doing validation because they are implementing that interface.

## Validating Query, Header, Cookie, and Param

For `Query`, `Header`, `Cookie`, and `Param` attributes, by default they choose validation rules by looking for its parameter type. For example:

```php
#[Get]
public function index(
    #[Query] int $limit,
    #[Query] int $offset,
    #[Query] ?string $keyword = null,
)
{
}
```

Evo will apply `required|numeric` to `$limit` and `$offset`, also apply rule `nullable|string` rule to `$keyword`.

But you can also use your own validation rules by giving `$rules` to attribute. For example:

```php
#[Get]
public function index(
    #[Query(rules: 'required|numeric|min:10')] int $limit,
    #[Query(rules: 'required|numeric|min:0')] int $offset,
    #[Query] ?string $keyword = null,
)
{
}
```

Now `$limit` and `$offset` parameters will use your defined rules instead of using the default rules.

## Validating Body

For `Body` attribute. It will scan its DTO properties, and choose rules for each properties by looking for the property type and finding `Illuminate\Contracts\Validation\Rule` attributes attached to it.

For example, if you have controller like this:

```php
#[Post('checkout')]
public function checkout(#[Body] CheckoutOrderDto $dto)
{
    // ...
}
```

Where `CheckoutOrderDto` has properties like this:

```php
<?php

namespace App\Dto;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Rules;

class CheckoutOrderDto extends Dto
{
    #[Rules\Required]
    #[Rules\Numeric]
    public string $phone;

    #[Rules\Required]
    public string $address;

    #[Rules\Required]
    #[Rules\Numeric]
    #[Rules\Size(11)]
    public string $subdistrict_id;

    #[Rules\Required]
    #[Rules\Numeric]
    #[Rules\Size(5)]
    public string $postal_code;

    #[ArrayOf(CheckoutOrderItemDto::class)]
    public array $items;
}
```

And `CheckoutOrderItemDto` like this:

```php
<?php

namespace App\Dto;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Rules;

class CheckoutOrderItemDto extends Dto
{
    #[Rules\Required]
    #[Rules\Exists('products', 'id')]
    public string $product_id;

    #[Rules\Required]
    #[Rules\Numeric]
    public int $qty;
}
```

The `Body` attribute will do this following validation for you:

```php
Validator::make($data, [
    'phone' => 'required|string|numeric',
    'address' => 'required|string',
    'subdistrict_id' => 'required|string|numeric|size:11',
    'postal_code' => 'required|string|numeric|size:5',
    'items' => 'required|array',
    'items.*.product_id' => 'required|string|exists:product,id',
    'items.*.qty' => 'required|numeric',
]);
```

## Create Your Own Validation Rule

Evo's validation rules is basically class implementing `Illuminate\Contracts\Validation\Rule` interface and has `Attribute` attribute attached on it.

For example, we will create `Bit` rule that only accepts string with "0" and "1" characters.

First, create `app/Rules/Bit.php` file.

Then write following code below:

```php
<?php

namespace App\Rules;

use Attribute;
use Illuminate\Contracts\Validation\Rule;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Bit implements Rule
{
    public function __construct(protected string $message = '')
    {
    }

    public function passes($attribute, $value)
    {
        return is_string($value) && preg_match("/^(0|1)+$/", $value);
    }

    public function message()
    {
        return __($this->message) ?: __("validation.bit") ?: "Invalid bit string";
    }
}
```

Now you can attach it to your DTO's property like this:

```php {7,12}
<?php

namespace App\Dto;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Rules;
use App\Rules\Bit;

class MyDto extends Dto
{
    #[Rules\Required]
    #[Bit]
    public string $bit_string;
}
```