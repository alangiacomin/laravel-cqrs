<?php

namespace Tests\Unit\App\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    public function test_flash_success_redirects_back_and_flashes_the_return_value(): void
    {
        $response = (new TestController())->flashSuccess('created');

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('created', session('success'));
    }

    public function test_spa_redirect_redirects_to_the_intended_route(): void
    {
        URL::useOrigin('http://localhost');

        $response = (new TestController())->spaRedirect('/target');

        $this->assertSame('http://localhost/target', $response->getTargetUrl());
    }

    public function test_hard_redirect_returns_an_inertia_location_response(): void
    {
        URL::useOrigin('http://localhost');
        Request::macro('inertia', fn (): bool => (bool) $this->header('X-Inertia'));
        request()->headers->set('X-Inertia', 'true');

        $response = (new TestController())->hardRedirect('/target');

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame('http://localhost/target', $response->headers->get('X-Inertia-Location'));
    }
}

class TestController extends Controller
{
}
