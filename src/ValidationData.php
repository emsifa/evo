<?php

namespace Emsifa\Evo;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;

class ValidationData implements ArrayAccess, Arrayable
{
    public function __construct(protected array $data)
    {
    }

    public function offsetSet($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function offsetGet($key)
    {
        return $this->offsetExists($key) ? $this->data[$key] : null;
    }

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    public function toArray()
    {
        return $this->data;
    }
}
