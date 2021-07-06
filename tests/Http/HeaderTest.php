<?php

namespace Emsifa\Evo\Tests\Http;

use Emsifa\Evo\Http\Header;
use Emsifa\Evo\Tests\Samples\Casters\HalfIntCaster;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ReflectionNamedType;
use ReflectionParameter;

class HeaderTest extends TestCase
{
    public function testGetValueFromHeader()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('xToken', 'string');
        $request = $this->makeRequestWithRouteHeaders(["x-token" => "abcde"]);
        $param = new Header();
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals("abcde", $result);
    }

    public function testGetValueFromHeaderWithDifferentKeyName()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('token', 'string');
        $request = $this->makeRequestWithRouteHeaders(["x-token" => "lorem-ipsum"]);
        $param = new Header('x-token');
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals('lorem-ipsum', $result);
    }

    public function testGetValueFromHeaderWithCaster()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', 'int');
        $request = $this->makeRequestWithRouteHeaders(["id" => "10"]);
        $param = new Header('id', caster: HalfIntCaster::class);
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
        $request = $this->makeRequestWithRouteHeaders(["id" => "120"]);
        $param = new Header('id', rules: 'numeric');

        $this->assertNull($param->validateRequest($request, $reflection));
    }

    public function testValidationError()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', 'int');
        $request = $this->makeRequestWithRouteHeaders(["id" => "im-not-a-number"]);
        $param = new Header('id', rules: 'numeric');

        $this->expectException(ValidationException::class);
        $param->validateRequest($request, $reflection);
    }

    private function makeRequestWithRouteHeaders(array $headers): Request
    {
        $request = new Request();
        foreach ($headers as $key => $value) {
            $request->headers->set($key, $value);
        }

        return $request;
    }

    private function getMockReflectionParam($name, string $typeName = '', $allowsNull = false)
    {
        if ($typeName) {
            $type = $this->createStub(ReflectionNamedType::class);
            $type->method('getName')->willReturn($typeName);
            $type->method('allowsNull')->willReturn($allowsNull);
        }

        $reflection = $this->createStub(ReflectionParameter::class);
        $reflection->method('getName')->willReturn($name);
        $reflection->method('getType')->willReturn($typeName ? $type : null);

        return $reflection;
    }
}
