<?php

namespace Alangiacomin\LaravelCqrs\EventHandlers;

use Alangiacomin\LaravelCqrs\Bus\BusHandler;
use Alangiacomin\LaravelCqrs\Events\IEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

abstract class EventHandler extends BusHandler implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(IEvent $event) : void
    {
        $this->handleObject($event);
    }

    public function shouldQueue(IEvent $event): bool
    {
        $classNameWithNamespace = get_class($this);
        $className = substr($classNameWithNamespace, strrpos($classNameWithNamespace, "\\") + 1);
        $handledEventName = trim($className, "Handler");

        return $event->name() == $handledEventName;
    }

    protected function setTypedObject(): void
    {
        $this->event = $this->busObject;
    }
}
