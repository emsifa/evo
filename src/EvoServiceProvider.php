<?php

namespace Emsifa\Evo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Emsifa\Evo\Commands\MakeDtoCommand;

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
}
