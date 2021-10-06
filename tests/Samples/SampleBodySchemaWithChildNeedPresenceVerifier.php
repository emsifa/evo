<?php

namespace Emsifa\Evo\Tests\Samples;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Types\ArrayOf;

class SampleBodySchemaWithChildNeedPresenceVerifier extends Dto
{
    public BodyTestUserDto $user;
}
