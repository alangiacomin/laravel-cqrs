<?php

namespace AlanGiacomin\LaravelCqrs;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class LaravelCqrsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        AboutCommand::add(
            'Laravel CQRS',
            fn () => [
                'Version' => InstalledVersions::getPrettyVersion('alangiacomin/laravel-cqrs'),
            ]
        );

        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                ]
            );
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/typescript-transformer.php',
            'typescript-transformer'
        );
    }
}
