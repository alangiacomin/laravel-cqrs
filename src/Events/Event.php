<?php

namespace Alangiacomin\LaravelCqrs\Events;

use Alangiacomin\LaravelCqrs\Bus\BusObject;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class Event extends BusObject implements IEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The event's broadcast name
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return $this->name();
    }
}
