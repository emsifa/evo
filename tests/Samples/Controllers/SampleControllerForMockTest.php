<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use DateTime;

class SampleControllerForMockTest
{
    public function nonResponsableReturn(): DateTime
    {
        return date_create();
    }
}
