<?php

namespace AlanGiacomin\LaravelCqrs\App\Infrastructure\Middleware;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes\GateAuthorize;
use Closure;
use Illuminate\Support\Facades\Gate;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class ApplyGateAttributes
{
    protected static array $cache = [];

    /**
     * @throws ReflectionException
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        $controller = $route->getController();
        $method = $route->getActionMethod();

        if ($controller) {
            $key = get_class($controller).'@'.$method;

            if (!isset(self::$cache[$key])) {
                $refClass = new ReflectionClass($controller);
                $refMethod = new ReflectionMethod($controller, $method);

                self::$cache[$key] = [
                    ...$refClass->getAttributes(GateAuthorize::class),
                    ...$refMethod->getAttributes(GateAuthorize::class),
                ];
            }

            foreach (self::$cache[$key] as $attr) {
                $instance = $attr->newInstance();
                Gate::authorize($instance->ability, $instance->arguments);
            }
        }

        return $next($request);
    }
}
