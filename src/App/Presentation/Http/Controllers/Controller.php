<?php

namespace AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    use AuthorizesRequests;

    public function flashSuccess(mixed $returnValue): RedirectResponse
    {
        return back()->with('success', $returnValue);
    }

    public function spaRedirect(string $route): RedirectResponse
    {
        return redirect()->intended($route);
    }

    public function hardRedirect(string $route): Response
    {
        return Inertia::location(redirect()->intended($route)->getTargetUrl());
    }
}
