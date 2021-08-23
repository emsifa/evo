<?php

namespace Emsifa\Evo\Tests\Helpers;

use Emsifa\Evo\Helpers\OpenApiHelper;
use Emsifa\Evo\Tests\Samples\Dto\OpenApiHelperDto;
use Emsifa\Evo\Tests\TestCase;
use ReflectionProperty;

class OpenApiHelperTest extends TestCase
{
    public function testMakeParameterFromProperty()
    {
        $property = new ReflectionProperty(OpenApiHelperDto::class, 'str');
        $result = OpenApiHelper::makeParameterFromProperty($property, 'str', 'query');

        $this->assertEquals('str', $result->name);
        $this->assertEquals('query', $result->in);
        $this->assertEquals('string', $result->schema->type);
        $this->assertEquals(true, $result->required);
    }
}
