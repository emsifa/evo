<?php

namespace Emsifa\Evo\Tests\Http;

use Emsifa\Evo\Http\Query;
use Emsifa\Evo\Tests\Samples\Casters\HalfIntCaster;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ReflectionNamedType;
use ReflectionProperty;

class QueryTest extends TestCase
{
    public function testGetValueFromQuery()
    {
        /**
         * @var \ReflectionProperty
         */
        $reflection = $this->getMockReflectionQuery('id', 'int');
        $request = $this->makeRequestWithRouteQueries(["id" => "10"]);
        $param = new Query();
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals(10, $result);
    }

    public function testGetValueFromQueryWithDifferentKeyName()
    {
        /**
         * @var \ReflectionProperty
         */
        $reflection = $this->getMockReflectionQuery('foo', 'string');
        $request = $this->makeRequestWithRouteQueries(["slug" => "lorem-ipsum"]);
        $param = new Query('slug');
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals('lorem-ipsum', $result);
    }

    public function testGetValueFromQueryWithCaster()
    {
        /**
         * @var \ReflectionProperty
         */
        $reflection = $this->getMockReflectionQuery('id', 'int');
        $request = $this->makeRequestWithRouteQueries(["id" => "10"]);
        $param = new Query('id', caster: new HalfIntCaster);
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals(5, $result);
        $this->assertEquals('integer', gettype($result));
    }

    public function testValidationSucceed()
    {
        /**
         * @var \ReflectionProperty
         */
        $reflection = $this->getMockReflectionQuery('id', 'int');
        $request = $this->makeRequestWithRouteQueries(["id" => "120"]);
        $param = new Query('id', rules: 'numeric');

        $this->assertNull($param->validateRequest($request, $reflection));
    }

    public function testValidationError()
    {
        /**
         * @var \ReflectionProperty
         */
        $reflection = $this->getMockReflectionQuery('id', 'int');
        $request = $this->makeRequestWithRouteQueries(["id" => "im-not-a-number"]);
        $param = new Query('id', rules: 'numeric');

        $this->expectException(ValidationException::class);
        $param->validateRequest($request, $reflection);
    }

    private function makeRequestWithRouteQueries(array $queries): Request
    {
        return new Request($queries);
    }

    private function getMockReflectionQuery($name, $type, $allowsNull = false)
    {
        $type = $this->createStub(ReflectionNamedType::class);
        $type->method('getName')->willReturn($type);
        $type->method('allowsNull')->willReturn($allowsNull);

        $reflection = $this->createStub(ReflectionProperty::class);
        $reflection->method('getName')->willReturn($name);
        $reflection->method('getType')->willReturn($type);

        return $reflection;
    }
}
