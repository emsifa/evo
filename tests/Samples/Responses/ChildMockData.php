<?php

namespace Emsifa\Evo\Tests\Samples\Responses;

use Emsifa\Evo\Http\Response\ResponseDto;

class ChildMockData extends ResponseDto
{
    public int $id;
    public string $creditCardNumber;
}
