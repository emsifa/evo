<?php

namespace Emsifa\Evo\Tests\Samples\Dto;

use Emsifa\Evo\Dto;
use Illuminate\Http\UploadedFile;

class SampleDtoWithFile extends Dto
{
    public int $int;
    public string $string;
    public bool $bool;
    public UploadedFile $file;
}
