<?php

namespace Alangiacomin\LaravelCqrs;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Alangiacomin\LaravelCqrs\LaravelCqrs
 */
class LaravelCqrsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-cqrs';
    }
}
