<?php

namespace Alangiacomin\LaravelCqrs;

use Alangiacomin\LaravelCqrs\Console\Commands\CreateCommand;
use Alangiacomin\LaravelCqrs\Console\Commands\CreateEvent;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCqrsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-cqrs')
            ->hasConfigFile()
            ->hasMigration('create_cqrs_jobs_table')
            ->hasCommands([
                CreateCommand::class,
                CreateEvent::class,
            ]);
    }
}
