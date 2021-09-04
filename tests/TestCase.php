<?php

namespace Emsifa\Evo\Tests;

use Emsifa\Evo\EvoServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use ReflectionNamedType;
use ReflectionParameter;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Emsifa\\Evo\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            EvoServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        include_once __DIR__.'/../database/migrations/create_evo_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }

    protected function getMockReflectionParam(
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
