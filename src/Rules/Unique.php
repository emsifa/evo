<?php

namespace Emsifa\Evo\Rules;

use Attribute;
use Emsifa\Evo\Contracts\HasPresenceVerifier;
use Emsifa\Evo\Rules\Concerns\PresenceVerifier;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Unique implements Rule, ImplicitRule, HasPresenceVerifier
{
    use ValidatesAttributes;
    use PresenceVerifier;

    protected $currentRule = '';

    public function __construct(
        protected string $table,
        protected string $column,
        protected ?string $ignore = null,
        protected string $idColumn = 'id',
        protected string $message = '',
    ) {
    }

    public function passes($attribute, $value)
    {
        return $this->validateUnique($attribute, $value, [
            $this->table,
            $this->column,
            $this->getIgnoreValue(),
            $this->idColumn
        ]);
    }

    protected function getIgnoreValue(): mixed
    {
        if (is_null($this->ignore)) {
            return null;
        }

        $split = explode(":", $this->ignore);
        if (count($split) != 2) {
            throw new InvalidArgumentException("Invalid Unique's \$ignore value: '{$this->ignore}'. Unique's \$ignore value must be a string with format 'source:key'. Eg: 'param:id'.");
        }

        [$source, $key] = $split;

        return match ($source) {
            "param" => request()->route($key),
            "cookie" => request()->cookie($key),
            "header" => request()->header($key),
            "query" => request()->query($key),
            "body" => request()->post($key),
            default => throw new InvalidArgumentException("Invalid Unique's ignore source: '{$source}'. Unique's ignore source can only be: param/cookie/header/query/body."),
        };
    }

    public function message()
    {
        return __($this->message) ?: __('validation.unique');
    }
}
