<?php

namespace Emsifa\Evo\Tests\Samples;

use Illuminate\Http\UploadedFile as HttpUploadedFile;

class UploadedFile extends HttpUploadedFile
{
    public function isValid()
    {
        return true;
    }
}
