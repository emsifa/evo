<?php

namespace Emsifa\Evo\Tests\Samples;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Rules\Exists;

class BodyTestUserDto extends Dto
{
    #[Exists('users', 'id')]
    public int $user_id;
}
