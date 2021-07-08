<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\ControllerDispatcher;
use Emsifa\Evo\Evo;
use Emsifa\Evo\Tests\Samples\Controllers\SampleController;
use Emsifa\Evo\Tests\Samples\DTO\PostStuffDTO;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Validation\ValidationException;
use ReflectionMethod;

class ControllerDispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function testResolveParameters()
    {
        $container = new Container;
        $dispatcher = new ControllerDispatcher($container);
        $controller = SampleController::class;
        $method = 'postStuff';

        $query = ['x' => 'lorem ipsum'];
        $data = [
            "age" => "20",
            "name" => "John Doe",
            "email" => "johndoe@mail.com",
        ];
        $request = new Request($query, $data, server: ['REQUEST_METHOD' => 'POST']);

        $result = $dispatcher->resolveParameters($request, $controller, $method);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('stuff', $result);
        $this->assertArrayHasKey('data', $result);

        $this->assertEquals('lorem ipsum', $result['stuff']);

        $this->assertInstanceOf(PostStuffDTO::class, $result['data']);
        $this->assertEquals(20, $result['data']->age);
        $this->assertEquals('John Doe', $result['data']->name);
        $this->assertEquals('johndoe@mail.com', $result['data']->email);
    }

    /**
     * @test
     */
    public function testResolveParametersWithInvalidInputShouldThrowsValidationError()
    {
        $container = new Container;
        $dispatcher = new ControllerDispatcher($container);
        $controller = SampleController::class;
        $method = 'postStuff';

        $query = ['x' => 'lorem ipsum'];
        $data = [
            "age" => "not a number",
            "name" => "John Doe",
            "email" => "johndoe@mail.com",
        ];
        $request = new Request($query, $data, server: ['REQUEST_METHOD' => 'POST']);

        $this->expectException(ValidationException::class);

        try {
            $dispatcher->resolveParameters($request, $controller, $method);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            $this->assertEquals(1, count($errors));
            $this->assertArrayHasKey('age', $errors);
            throw $e;
        }
    }
}
