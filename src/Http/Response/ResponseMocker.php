<?php

namespace Emsifa\Evo\Http\Response;

use Emsifa\Evo\Contracts\Mockable;
use Emsifa\Evo\Contracts\ValueFaker;
use Emsifa\Evo\DTO\FakesCount;
use Emsifa\Evo\DTO\UseFaker;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Emsifa\Evo\Helpers\TypeHelper;
use Emsifa\Evo\ObjectFiller;
use Emsifa\Evo\Types\ArrayOf;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

class ResponseMocker
{
    public function __construct(protected Container $container)
    {
    }

    public function mock(ReflectionClass $reflectionClass, Request $request): Responsable
    {
        /**
         * @var Responsable $response
         */
        $response = $reflectionClass->newInstance();
        $faker = Factory::create(config('app.faker_locale', 'en_US'));

        $mockedData = $response instanceof Mockable
            ? $response->getMockedData($faker, $request)
            : $this->getMockedData($reflectionClass, $faker, $request);

        ObjectFiller::fillObject($response, $mockedData);

        return $response;
    }

    protected function getMockedData(
        ReflectionClass $reflectionClass,
        Generator $faker,
        Request $request,
    ): array {
        $data = [];
        $props = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            $name = $prop->getName();
            $value = $this->getFakeValue($prop, $faker, $request);
            $data[$name] = $value;
        }

        return $data;
    }

    public function getFakeValue(ReflectionProperty $prop, Generator $faker, Request $request): mixed
    {
        $key = $prop->getName();
        $type = $prop->getType();
        $isArray = $type && $type->getName() === "array";
        $isBuiltIn = $type && $type->isBuiltin();
        $typeName = $type ? $type->getName() : null;
        $isObject = $typeName && ! $isBuiltIn && class_exists($typeName);

        // Resolve array values
        if ($isArray) {
            return $this->generateSomeFakeValues($prop, $faker);
        }

        // Resolve object type
        // For example we got property: public PostDTO $post
        // This should return mock for PostDTO values
        if ($isObject) {
            return $this->generateObjectValue($typeName, $faker, $request, $key);
        }

        // If property name present in request data
        // And request value can be filled to property according its type
        // Return request value
        $requestValue = $request->get($key);
        if ($requestValue && $this->canFill($prop, $requestValue)) {
            return $requestValue;
        }

        // If property is using `UseFaker` attribute
        // Get that attribute
        // And return faker generated value
        /**
         * @var UseFaker|null $useFaker
         */
        $useFaker = ReflectionHelper::getFirstAttributeInstance($prop, UseFaker::class, ReflectionAttribute::IS_INSTANCEOF);
        if ($useFaker) {
            $method = $useFaker->getFakerMethodName();
            $args = $useFaker->getArgs();

            if ($this->fakerHasFormatter($faker, $method)) {
                return call_user_func_array([$faker, $method], $args);
            }

            if (class_exists($method) && is_subclass_of($method, ValueFaker::class, true)) {
                /**
                 * @var ValueFaker $instance
                 */
                $instance = $this->container->make($method, $args);
                return $instance->generateFakeValue($faker, $prop);
            }

            throw new InvalidArgumentException("Cannot generate UseFaker value: '{$method}'. Faker formatter or ValueFaker doesn't exists.");
        }

        // If `$key` available in faker generator
        // Generate faker value using method `$key`-name
        if ($this->fakerHasFormatter($faker, $key)) {
            return call_user_func([$faker, $key]);
        }

        // Lastly, generate default fake value
        // According property type name
        return $this->generateScalarValue($typeName ?: "", $faker);
    }

    protected function canFill(ReflectionProperty $prop, mixed $value): bool
    {
        $type = $prop->getType();
        $typeName = $type ? $type->getName() : null;

        // When property type is null
        // It could have any value
        if (! $type) {
            return true;
        }

        return (
            (is_numeric($value) && in_array($typeName, ["int", "float"]))
            || (is_string($value) && $typeName === "string")
            || (is_array($value) && $type === "array")
            || (is_bool($value) && $type === "bool")
            || ($type->allowsNull() && is_null($value))
        );
    }

    protected function generateSomeFakeValues(ReflectionProperty $prop, Generator $faker)
    {
        /**
         * @var FakesCount $fakesCountAttr
         */
        $fakesCountAttr = ReflectionHelper::getFirstAttributeInstance($prop, FakesCount::class, ReflectionAttribute::IS_INSTANCEOF);
        $count = $fakesCountAttr ? $fakesCountAttr->getCount() : rand(5, 10);

        /**
         * @var ArrayOf $arrayOfAttr
         */
        $arrayOfAttr = ReflectionHelper::getFirstAttributeInstance($prop, ArrayOf::class, ReflectionAttribute::IS_INSTANCEOF);
        $typeName = $arrayOfAttr ? $arrayOfAttr->getType() : "int";
        $isBuiltIn = TypeHelper::isBuiltInType($typeName);
        $isObject = $typeName && ! $isBuiltIn && class_exists($typeName);

        return collect(range(1, $count))->map(function () use ($typeName, $isObject, $faker) {
            return $isObject
                ? $this->generateObjectValue($typeName, $faker)
                : $this->generateScalarValue($typeName, $faker);
        });
    }

    protected function generateObjectValue(
        string $typeName,
        Generator $faker,
        ?Request $request = null,
        mixed $key = null
    ): array {
        $reflectionClass = new ReflectionClass($typeName);
        $requestValue = $request ? $request->get($key) : [];
        $childRequest = new Request(request: (array) ($requestValue ?: []));

        return $this->getMockedData($reflectionClass, $faker, $childRequest);
    }

    protected function generateScalarValue(string $typeName, Generator $faker): mixed
    {
        return match ($typeName) {
            "int" => $faker->numberBetween(1, 1000),
            "float" => $faker->randomFloat(3, 0, 1000),
            "string" => $faker->words(rand(5, 10), true),
            "bool" => $faker->boolean(50),
            default => $faker->words(rand(5, 10), true),
        };
    }

    protected function fakerHasFormatter(Generator $generator, string $formatter): bool
    {
        try {
            $generator->getFormatter($formatter);

            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
}
