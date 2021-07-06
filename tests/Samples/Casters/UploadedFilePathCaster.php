<?php

namespace Emsifa\Evo\Tests\Samples\Casters;

use Attribute;
use Emsifa\Evo\Contracts\Caster;
use Emsifa\Evo\Exceptions\CastErrorException;
use Illuminate\Http\UploadedFile;
use ReflectionParameter;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class UploadedFilePathCaster implements Caster
{
    public function cast($value, ReflectionProperty | ReflectionParameter $prop): mixed
    {
        if ($value instanceof UploadedFile) {
            return $value->getPathname();
        }

        throw new CastErrorException("UploadedFilePathCaster error");
    }
}
