<?php

namespace Alangiacomin\LaravelCqrs\CommandHandlers;

use Alangiacomin\LaravelCqrs\Bus\Bus;
use Alangiacomin\LaravelCqrs\Bus\BusHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class CommandHandler extends BusHandler implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct($busObject)
    {
        $this->busObject = $busObject;
    }

    public function publish($event): void
    {
        Bus::sendEvent($event);
    }

    public function handle(): void
    {
        $this->handleObject($this->busObject);
    }

    protected function setTypedObject(): void
    {
        $this->command = $this->busObject;
    }
}
