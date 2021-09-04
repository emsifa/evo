<?php

namespace Emsifa\Evo\Tests\Http;

use Emsifa\Evo\Http\Cookie;
use Emsifa\Evo\Tests\Samples\Casters\HalfIntCaster;
use Emsifa\Evo\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ReflectionNamedType;
use ReflectionParameter;

class CookieTest extends TestCase
{
    public function testGetValueFromCookie()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('token', 'string');
        $request = $this->makeRequestWithCookies(["token" => "qwerty"]);
        $param = new Cookie();
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals("qwerty", $result);
    }

    public function testGetValueFromCookieWithDifferentKeyName()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('foo', 'string');
        $request = $this->makeRequestWithCookies(["token" => "lorem-ipsum"]);
        $param = new Cookie('token');
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals('lorem-ipsum', $result);
    }

    public function testGetValueFromCookieWithCaster()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('number', 'int');
        $request = $this->makeRequestWithCookies(["number" => "10"]);
        $param = new Cookie('number', caster: HalfIntCaster::class);
        $result = $param->getRequestValue($request, $reflection);

        $this->assertEquals(5, $result);
        $this->assertEquals('integer', gettype($result));
    }

    public function testValidationSucceed()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('number', 'int');
        $request = $this->makeRequestWithCookies(["number" => "120"]);
        $param = new Cookie('number', rules: 'numeric');

        $this->assertNull($param->validateRequest($request, $reflection));
    }

    public function testValidationError()
    {
        /**
         * @var \ReflectionParameter
         */
        $reflection = $this->getMockReflectionParam('number', 'int');
        $request = $this->makeRequestWithCookies(["number" => "im-not-a-number"]);
        $param = new Cookie('number', rules: 'numeric');

        $this->expectException(ValidationException::class);
        $param->validateRequest($request, $reflection);
    }

    private function makeRequestWithCookies(array $cookies): Request
    {
        return new Request(cookies: $cookies);
    }
}
