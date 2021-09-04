<?php

namespace Emsifa\Evo\Tests\Http;

use Emsifa\Evo\Http\Query;
use Emsifa\Evo\Tests\Samples\Casters\HalfIntCaster;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ReflectionNamedType;
use ReflectionParameter;

class QueryTest extends TestCase
{
    public function testGetValueFromQuery()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', 'int');
        $request = $this->makeRequestWithRouteQueries(["id" => "10"]);
        $param = new Query();
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals(10, $result);
    }

    public function testGetValueFromQueryWithDifferentKeyName()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('foo', 'string');
        $request = $this->makeRequestWithRouteQueries(["slug" => "lorem-ipsum"]);
        $param = new Query('slug');
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals('lorem-ipsum', $result);
    }

    public function testGetValueFromQueryWithCaster()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', 'int');
        $request = $this->makeRequestWithRouteQueries(["id" => "10"]);
        $param = new Query('id', caster: HalfIntCaster::class);
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals(5, $result);
        $this->assertEquals('integer', gettype($result));
    }

    public function testValidationSucceed()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', 'int');
        $request = $this->makeRequestWithRouteQueries(["id" => "120"]);
        $param = new Query('id', rules: 'numeric');

        $this->assertNull($param->validateRequest($request, $reflection));
    }

    public function testValidationError()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', 'int');
        $request = $this->makeRequestWithRouteQueries(["id" => "im-not-a-number"]);
        $param = new Query('id', rules: 'numeric');

        $this->expectException(ValidationException::class);
        $param->validateRequest($request, $reflection);
    }

    public function testGetAssignedDefaultValue()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', 'int', false, 123);
        $request = $this->makeRequestWithRouteQueries([]);
        $param = new Query('id');
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals(123, $result);
    }

    private function makeRequestWithRouteQueries(array $queries): Request
    {
        return new Request($queries);
    }

    private function getMockReflectionParam(
        $name,
        string $typeName = '',
        $allowsNull = false,
        $defaultValue = null,
    ) {
        if ($typeName) {
            $type = $this->createStub(ReflectionNamedType::class);
            $type->method('getName')->willReturn($typeName);
            $type->method('allowsNull')->willReturn($allowsNull);
        }

        $reflection = $this->createStub(ReflectionParameter::class);
        $reflection->method('getName')->willReturn($name);
        $reflection->method('getType')->willReturn($typeName ? $type : null);
        $reflection->method('isDefaultValueAvailable')->willReturn($defaultValue !== null);
        $reflection->method('getDefaultValue')->willReturn($defaultValue);

        return $reflection;
    }
}
