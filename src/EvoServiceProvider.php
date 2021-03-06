<?php

namespace Emsifa\Evo;

use Emsifa\Evo\Commands\MakeDtoCommand;
use Emsifa\Evo\Commands\MakeResponseCommand;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;
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
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets()
            ->hasCommand(MakeDtoCommand::class)
            ->hasCommand(MakeResponseCommand::class);
    }

    public function registeringPackage()
    {
        $this->app->singleton(Evo::class, function () {
            return new Evo(app(Router::class), app(Container::class));
        });

        // @codeCoverageIgnoreStart
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            $this->app->bind(ControllerDispatcherContract::class, function () {
                return new ControllerDispatcher($this->app);
            });
        }
        // @codeCoverageIgnoreEnd

        $this->app->bind('evo', fn () => $this->app->make(Evo::class));
    }
}
