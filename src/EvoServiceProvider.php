<?php

namespace Emsifa\Evo;

use Emsifa\Evo\Commands\MakeDtoCommand;
use Illuminate\Container\Container;
use Illuminate\Routing\Router;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EvoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('evo')
            // ->hasConfigFile()
            // ->hasViews()
            // ->hasMigration('create_evo_table')
            ->hasCommand(MakeDtoCommand::class);
    }

    public function registeringPackage()
    {
        $this->app->bind('evo', fn() => $this->app->make(Evo::class));
    }
}
