<?php

namespace Alangiacomin\LaravelCqrs\Controllers;

use Alangiacomin\LaravelCqrs\Bus\Bus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function executeCommand($command): void
    {
        Bus::sendCommand($command);
    }
}
