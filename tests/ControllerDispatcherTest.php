<?php

namespace Emsifa\Evo\Tests;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Emsifa\Evo\ControllerDispatcher;
use Emsifa\Evo\Tests\Samples\Controllers\SampleController;
use Emsifa\Evo\Tests\Samples\Controllers\SampleDispatchedController;
use Emsifa\Evo\Tests\Samples\DTO\PostStuffDTO;
use Emsifa\Evo\Tests\Samples\Exceptions\CustomException;
use Emsifa\Evo\Tests\Samples\Responses\SampleCustomErrorResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleErrorResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleInvalidResponse;
use Emsifa\Evo\Tests\Samples\Responses\SampleSuccessResponse;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use InvalidArgumentException;
use ReflectionMethod;
use UnexpectedValueException;

class ControllerDispatcherTest extends TestCase
{
    use ArraySubsetAsserts;

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

    public function testReturnMockedResponse()
    {
        $controller = new SampleDispatchedController;
        $dispatcher = new ControllerDispatcher($this->app);
        $route = new Route('POST', '/', SampleDispatchedController::class.'@methodWithMock');
        $result = $dispatcher->dispatch($route, $controller, 'methodWithMock');

        $this->assertInstanceOf(SampleSuccessResponse::class, $result);
        $this->assertTrue(in_array($result->id, [1, 2, 3]));
        $this->assertTrue(in_array($result->name, ["John Doe", "Jane Doe"]));
    }

    public function testOptionalMockShouldNotReturnMockIfDoesntHasMockQuery()
    {
        $controller = new SampleDispatchedController;
        $dispatcher = new ControllerDispatcher($this->app);
        $route = new Route('POST', '/', SampleDispatchedController::class.'@methodWithOptionalMock');
        $result = $dispatcher->dispatch($route, $controller, 'methodWithOptionalMock');

        $this->assertInstanceOf(SampleSuccessResponse::class, $result);
        $this->assertEquals($result->id, 456);
        $this->assertEquals($result->name, "Nikola Tesla");
    }

    public function testOptionalMockShouldReturnMockedResponseIfHasMockQuery()
    {
        $this->app->bind(Request::class, function () {
            return new Request(query: ['_mock' => '1']);
        });
        $controller = new SampleDispatchedController;
        $dispatcher = new ControllerDispatcher($this->app);
        $route = new Route('POST', '/', SampleDispatchedController::class.'@methodWithOptionalMock');
        $result = $dispatcher->dispatch($route, $controller, 'methodWithOptionalMock');

        $this->assertInstanceOf(SampleSuccessResponse::class, $result);
        $this->assertTrue(in_array($result->id, [1, 2, 3]));
        $this->assertTrue(in_array($result->name, ["John Doe", "Jane Doe"]));
    }

    public function testIgnoreMockWhenIgnoreMockConfigIsTrue()
    {
        config(['evo' => ['ignore_mock' => true]]);

        $controller = new SampleDispatchedController;
        $dispatcher = new ControllerDispatcher($this->app);
        $route = new Route('POST', '/', SampleDispatchedController::class.'@methodWithMock');
        $result = $dispatcher->dispatch($route, $controller, 'methodWithMock');

        $this->assertInstanceOf(SampleSuccessResponse::class, $result);
        $this->assertEquals($result->id, 789);
        $this->assertEquals($result->name, "John Doe");
    }

    public function testDontIgnoreMockWhenIgnoreMockConfigIsFalse()
    {
        config(['evo' => ['ignore_mock' => false]]);

        $controller = new SampleDispatchedController;
        $dispatcher = new ControllerDispatcher($this->app);
        $route = new Route('POST', '/', SampleDispatchedController::class.'@methodWithMock');
        $result = $dispatcher->dispatch($route, $controller, 'methodWithMock');

        $this->assertInstanceOf(SampleSuccessResponse::class, $result);
        $this->assertTrue(in_array($result->id, [1, 2, 3]));
        $this->assertTrue(in_array($result->name, ["John Doe", "Jane Doe"]));
    }

    public function testGetErrorResponseMap()
    {
        $controller = new SampleDispatchedController;
        $dispatcher = new ControllerDispatcher($this->app);

        $result = $dispatcher->getErrorResponsesMap(new ReflectionMethod($controller, "methodWithSpecificErrorResponse"));

        $this->assertArraySubset($result, [
            InvalidArgumentException::class => SampleCustomErrorResponse::class,
            ValidationException::class => SampleInvalidResponse::class,
            '_' => SampleErrorResponse::class,
        ]);
    }

    /**
     * @dataProvider findBestMatchProvider
     */
    public function testFindBestMatchErrorResponseShouldReturnCorrectResponseClass($exception, $responseClass)
    {
        $controller = new SampleDispatchedController;
        $dispatcher = new ControllerDispatcher($this->app);
        $responseMap = $dispatcher->getErrorResponsesMap(new ReflectionMethod($controller, "methodWithSpecificErrorResponse"));

        $result = $dispatcher->findBestMatchErrorResponse($exception, $responseMap);

        $this->assertEquals($responseClass, $result);
    }

    public function findBestMatchProvider()
    {
        $translator = new Translator(new ArrayLoader(), 'en');
        $validator = new Validator($translator, [], []);
        return [
            [new InvalidArgumentException, SampleCustomErrorResponse::class],
            [new ValidationException($validator), SampleInvalidResponse::class],
            [new CustomException, SampleCustomErrorResponse::class],
            [new UnexpectedValueException, SampleErrorResponse::class],
        ];
    }
}
