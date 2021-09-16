<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\Rules\Unique;
use Emsifa\Evo\Tests\Samples\MockPresenceVerifier;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use ReflectionMethod;

class UniqueTest extends TestCase
{
    public function testItShouldBeValidIfDataUnique()
    {
        $mockPresenceVerifier = new MockPresenceVerifier(collect([
            'users' => collect([
                ['username' => 'loremipsum'],
            ]),
        ]));

        $data = [
            'username' => 'foobar',
        ];

        $unique = new Unique('users', 'username');
        $unique->setPresenceVerifier($mockPresenceVerifier);

        $validator = Validator::make($data, [
            'username' => [$unique],
        ]);

        $this->assertFalse($validator->fails());
    }

    public function testItShouldBeInvalidIfDataDoesNotUnique()
    {
        $mockPresenceVerifier = new MockPresenceVerifier(collect([
            'users' => collect([
                ['username' => 'foobar'],
            ]),
        ]));

        $data = [
            'username' => 'foobar',
        ];

        $unique = new Unique('users', 'username');
        $unique->setPresenceVerifier($mockPresenceVerifier);

        $validator = Validator::make($data, [
            'username' => [$unique],
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testOverrideMessage()
    {
        $message = 'opps invalid value';
        $rule = new Unique('users', 'username', message: $message);

        $this->assertEquals($message, $rule->message());
    }

    public function testFallbackMessage()
    {
        $rule = new Unique('users', 'username');

        $this->assertEquals(__('validation.unique'), $rule->message());
    }

    public function testIgnoreValueWithWrongFormatShouldThrowException()
    {
        $mockPresenceVerifier = new MockPresenceVerifier(collect([]));
        $data = [];
        $unique = new Unique('users', 'username', 'no-separator');
        $unique->setPresenceVerifier($mockPresenceVerifier);

        $validator = Validator::make($data, [
            'username' => [$unique],
        ]);

        $this->expectExceptionMessage("Invalid Unique's \$ignore value: 'no-separator'. Unique's \$ignore value must be a string with format 'source:key'. Eg: 'param:id'.");

        $validator->fails();
    }

    public function testIgnoreValueWithNotSupportedSourceShouldThrowException()
    {
        $mockPresenceVerifier = new MockPresenceVerifier(collect([]));
        $data = [];
        $unique = new Unique('users', 'username', 'foo:id');
        $unique->setPresenceVerifier($mockPresenceVerifier);

        $validator = Validator::make($data, [
            'username' => [$unique],
        ]);

        $this->expectExceptionMessage("Invalid Unique's ignore source: 'foo'. Unique's ignore source can only be: param/cookie/header/query/body.");

        $validator->fails();
    }

    public function testGetIgnoreValueFromParam()
    {
        /**
         * @var Request
         */
        $request = $this->app->make(Request::class);
        $request->setRouteResolver(function () {
            $route = new Route('GET', '/', 'any@thing');
            $route->parameters = ['id' => 123];
            return $route;
        });

        $unique = new Unique('users', 'username', 'param:id');
        $getIgnoreValue = new ReflectionMethod($unique, "getIgnoreValue");
        $getIgnoreValue->setAccessible(true);

        $value = $getIgnoreValue->invoke($unique);
        $this->assertEquals(123, $value);
    }

    public function testGetIgnoreValueFromQuery()
    {
        /**
         * @var Request
         */
        $request = $this->app->make(Request::class);
        $request->query->add(["id" => 123]);

        $unique = new Unique('users', 'username', 'query:id');
        $getIgnoreValue = new ReflectionMethod($unique, "getIgnoreValue");
        $getIgnoreValue->setAccessible(true);

        $value = $getIgnoreValue->invoke($unique);
        $this->assertEquals(123, $value);
    }

    public function testGetIgnoreValueFromHeader()
    {
        /**
         * @var Request
         */
        $request = $this->app->make(Request::class);
        $request->headers->add(["thing" => 123]);

        $unique = new Unique('users', 'username', 'header:thing');
        $getIgnoreValue = new ReflectionMethod($unique, "getIgnoreValue");
        $getIgnoreValue->setAccessible(true);

        $value = $getIgnoreValue->invoke($unique);
        $this->assertEquals(123, $value);
    }

    public function testGetIgnoreValueFromCookie()
    {
        /**
         * @var Request
         */
        $request = $this->app->make(Request::class);
        $request->cookies->add(["thing" => 123]);

        $unique = new Unique('users', 'username', 'cookie:thing');
        $getIgnoreValue = new ReflectionMethod($unique, "getIgnoreValue");
        $getIgnoreValue->setAccessible(true);

        $value = $getIgnoreValue->invoke($unique);
        $this->assertEquals(123, $value);
    }

    public function testGetIgnoreValueFromBody()
    {
        /**
         * @var Request
         */
        $request = $this->app->make(Request::class);
        $request->request->add(["thing" => 123]);

        $unique = new Unique('users', 'username', 'body:thing');
        $getIgnoreValue = new ReflectionMethod($unique, "getIgnoreValue");
        $getIgnoreValue->setAccessible(true);

        $value = $getIgnoreValue->invoke($unique);
        $this->assertEquals(123, $value);
    }
}
