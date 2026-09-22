<?php

namespace Tests\Unit\App\Infrastructure\Middleware;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes\GateAuthorize;
use AlanGiacomin\LaravelCqrs\App\Infrastructure\Middleware\ApplyGateAttributes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ApplyGateAttributesTest extends TestCase
{
    public function test_middleware_authorizes_attributes_defined_on_controller_and_method(): void
    {
        $calls = [];

        Gate::before(function (mixed $user, string $ability, array $arguments) use (&$calls): bool {
            $calls[] = [$ability, $arguments];

            return true;
        });

        $request = Request::create('/posts/42', 'GET');
        $route = new class() {
            public function getController(): object
            {
                return new AuthorizedController();
            }

            public function getActionMethod(): string
            {
                return 'show';
            }
        };
        $request->setRouteResolver(fn () => $route);

        $response = (new ApplyGateAttributes())->handle($request, fn () => 'next');

        $this->assertSame('next', $response);
        $this->assertSame([
            ['manage-posts', []],
            ['view-post', ['id' => 42]],
        ], $calls);
    }

    public function test_middleware_continues_when_route_has_no_controller(): void
    {
        $request = Request::create('/health', 'GET');
        $route = new class() {
            public function getController(): null
            {
                return null;
            }

            public function getActionMethod(): string
            {
                return '__invoke';
            }
        };
        $request->setRouteResolver(fn () => $route);

        $this->assertSame(
            'next',
            (new ApplyGateAttributes())->handle($request, fn () => 'next')
        );
    }
}

#[GateAuthorize('manage-posts')]
class AuthorizedController
{
    #[GateAuthorize('view-post', ['id' => 42])]
    public function show(): string
    {
        return 'ok';
    }
}
