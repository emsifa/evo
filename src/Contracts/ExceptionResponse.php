<?php

namespace Emsifa\Evo\Contracts;

use Exception;

interface ExceptionResponse
{
    public function forException(Exception $exception): static;
}
