<?php

namespace Alangiacomin\LaravelCqrs\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        $defaultValue = true;
        $configValue = config('cqrs.eventListener.shouldDiscoverEvents');

        return isset($configValue) && is_bool($configValue)
            ? $configValue
            : $defaultValue;
    }

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array
     */
    protected function discoverEventsWithin()
    {
        $defaultValue = [];
        $configValue = config('cqrs.eventListener.directories');
        if (is_string($configValue)) {
            $configValue = [$configValue];
        }

        if (!isset($configValue) || !is_array($configValue)) {
            return $defaultValue;
        }

        return array_map(function ($c) {
            $rp = realpath($c);
            return $rp == false
                ? $this->app->path($c)
                : $rp;
        }, $configValue);
    }
}
