<?php

namespace Emsifa\Evo\Tests\Http;

use Emsifa\Evo\Http\Param;
use Emsifa\Evo\Tests\Samples\Casters\HalfIntCaster;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;

class ParamTest extends TestCase
{
    public function testGetValueFromParam()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', 'int');
        $request = $this->makeRequestWithRouteParams(["id" => "10"]);
        $param = new Param();
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals(10, $result);
    }

    public function testGetValueFromParamWithDifferentKeyName()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('foo', 'string');
        $request = $this->makeRequestWithRouteParams(["slug" => "lorem-ipsum"]);
        $param = new Param('slug');
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals('lorem-ipsum', $result);
    }

    public function testGetValueFromParamWithCaster()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', 'int');
        $request = $this->makeRequestWithRouteParams(["id" => "10"]);
        $param = new Param('id', caster: HalfIntCaster::class);
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
        $request = $this->makeRequestWithRouteParams(["id" => "120"]);
        $param = new Param('id', rules: 'numeric');

        $this->assertNull($param->validateRequest($request, $reflection));
    }

    public function testValidationError()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', 'int');
        $request = $this->makeRequestWithRouteParams(["id" => "im-not-a-number"]);
        $param = new Param('id', rules: 'numeric');

        $this->expectException(ValidationException::class);
        $param->validateRequest($request, $reflection);
    }

    public function testGetAssignedDefaultValue()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('id', 'int', false, 123);
        $request = $this->makeRequestWithRouteParams([]);
        $param = new Param('id');
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals(123, $result);
    }

    private function makeRequestWithRouteParams(array $params): Request
    {
        $request = new Request();
        $request->setRouteResolver(function () use ($params) {
            $route = new Route('POST', '/', []);
            $route->parameters = $params;

            return $route;
        });

        return $request;
    }
}
