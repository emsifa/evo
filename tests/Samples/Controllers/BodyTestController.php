<?php

namespace Emsifa\Evo\Tests\Samples\Controllers;

use Emsifa\Evo\Tests\Samples\SampleBodySchema;
use Emsifa\Evo\Tests\Samples\SampleBodySchemaWithChildNeedPresenceVerifier;
use Illuminate\Routing\Controller;

class BodyTestController extends Controller
{
    public function methodWithMixedParam($data)
    {
    }

    public function methodWithNonDtoParam(SampleBodySchema $data)
    {
    }

    public function methodWithPresenceVerifierInChildObjects(SampleBodySchemaWithChildNeedPresenceVerifier $data)
    {
    }
}
