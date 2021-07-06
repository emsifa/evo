<?php

namespace Emsifa\Evo\Tests\Samples;

class ChildObject
{
    public int $number;
    public string $text;
}

class SimpleNestedObject
{
    public ChildObject $child;
}
