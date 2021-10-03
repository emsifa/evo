<?php

namespace Emsifa\Evo\Tests\Samples\Exceptions;

use Emsifa\Evo\Http\Response\ResponseStatus;
use Exception;

#[ResponseStatus(402)]
class CustomExceptionWithResponseStatus extends Exception
{
}
