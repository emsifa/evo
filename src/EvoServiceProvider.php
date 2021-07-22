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
            // ->hasMigration('create_evo_table')
            ->hasViews()
            ->hasAssets()
            ->hasCommand(MakeDtoCommand::class);
    }

    public function registeringPackage()
    {
        $this->app->singleton(Evo::class, function () {
            return new Evo(app(Router::class), app(Container::class));
        });

        $this->app->bind('evo', fn () => $this->app->make(Evo::class));
    }
}
